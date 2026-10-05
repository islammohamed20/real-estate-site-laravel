/**
 * 2D Site Plan — Pure Canvas rendering (no Three.js)
 * Shows buildings, floors, and unit statuses from bird's eye view.
 */

const root = document.getElementById('project-3d-canvas');
if (!root) {
    console.warn('[site-plan] Canvas container not found');
} else {
    try {
        initSitePlan();
    } catch (err) {
        console.error('[site-plan] Failed:', err);
        root.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:14px;text-align:center;padding:2rem;">تعذّر تحميل المخطط.<br><span style="font-size:12px;color:#64748b">' + (err.message || err) + '</span></div>';
    }
}

function initSitePlan() {
    const data = window.__projectLayoutData || { buildings: [] };
    const onSelectUnit = (unit) => window.__selectProjectUnit?.(unit);

    // Create canvas
    const canvas = document.createElement('canvas');
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.cursor = 'default';
    root.appendChild(canvas);
    const ctx = canvas.getContext('2d');

    // State
    let hoveredUnit = null;
    let selectedUnit = null;
    let scale = 1;
    let offsetX = 0;
    let offsetY = 0;
    let isDragging = false;
    let dragStart = { x: 0, y: 0 };
    let lastOffset = { x: 0, y: 0 };

    // Animation state
    let animFrame = null;
    let animFrom = { x: 0, y: 0, s: 1 };
    let animTarget = { x: 0, y: 0, s: 1 };
    let animStart = 0;
    const ANIM_DURATION = 400;

    // Easing: ease-out cubic
    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    function animateTo(targetX, targetY, targetScale) {
        if (animFrame) cancelAnimationFrame(animFrame);
        animFrom = { x: offsetX, y: offsetY, s: scale };
        animTarget = { x: targetX, y: targetY, s: targetScale };
        animStart = performance.now();

        function tick(now) {
            const elapsed = now - animStart;
            const progress = Math.min(elapsed / ANIM_DURATION, 1);
            const ease = easeOutCubic(progress);

            offsetX = animFrom.x + (animTarget.x - animFrom.x) * ease;
            offsetY = animFrom.y + (animTarget.y - animFrom.y) * ease;
            scale = animFrom.s + (animTarget.s - animFrom.s) * ease;

            draw();

            if (progress < 1) {
                animFrame = requestAnimationFrame(tick);
            } else {
                animFrame = null;
            }
        }
        animFrame = requestAnimationFrame(tick);
    }

    // Colors
    const COLORS = {
        bg: '#142218',
        grass: '#1a3a28',
        grassLight: '#1f4430',
        road: '#2a2a2a',
        roadLine: '#444',
        sidewalk: '#3a3a3a',
        buildingBg: '#2a3a30',
        buildingBorder: '#3d5a48',
        buildingLabel: '#fbbf24',
        unitHover: 'rgba(255,255,255,0.25)',
        unitSelect: 'rgba(251,191,36,0.4)',
        unitText: '#fff',
        pool: '#0ea5e9',
        poolLight: '#38bdf8',
        tree: '#166534',
        treeLight: '#22c55e',
        gate: '#fbbf24',
        text: '#e2e8f0',
        textDim: '#64748b',
    };

    const STATUS_COLORS = {
        available: '#10b981',
        reserved: '#f59e0b',
        sold: '#e11d48',
        hidden: '#475569',
    };

    // Collect all units for hit testing
    let allUnits = [];
    let buildingRects = [];

    function resize() {
        const rect = root.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    // ===== Drawing Functions =====

    function drawBackground(w, h) {
        // Main background
        ctx.fillStyle = COLORS.bg;
        ctx.fillRect(0, 0, w, h);

        // Grass texture (subtle grid)
        ctx.fillStyle = COLORS.grass;
        ctx.fillRect(0, 0, w, h);

        // Subtle grass pattern
        ctx.fillStyle = COLORS.grassLight;
        for (let x = 0; x < w; x += 20) {
            for (let y = 0; y < h; y += 20) {
                if ((x + y) % 40 === 0) {
                    ctx.fillRect(x, y, 10, 10);
                }
            }
        }
    }

    function drawRoad(cx, cy, compoundW, compoundH) {
        const roadW = 16;
        const r = compoundW / 2 + roadW + 10;

        // Outer road (oval)
        ctx.beginPath();
        ctx.ellipse(cx, cy, r, compoundH / 2 + roadW + 10, 0, 0, Math.PI * 2);
        ctx.strokeStyle = COLORS.road;
        ctx.lineWidth = roadW * 2;
        ctx.stroke();

        // Road center line (dashed)
        ctx.setLineDash([8, 8]);
        ctx.beginPath();
        ctx.ellipse(cx, cy, r, compoundH / 2 + roadW + 10, 0, 0, Math.PI * 2);
        ctx.strokeStyle = COLORS.roadLine;
        ctx.lineWidth = 1.5;
        ctx.stroke();
        ctx.setLineDash([]);

        // Entry road (bottom)
        ctx.fillStyle = COLORS.road;
        ctx.fillRect(cx - 12, cy + compoundH / 2 + roadW + 5, 24, 30);

        // Gate
        ctx.fillStyle = COLORS.gate;
        ctx.fillRect(cx - 16, cy + compoundH / 2 + roadW + 2, 32, 6);
        ctx.fillStyle = COLORS.gate;
        ctx.beginPath();
        ctx.arc(cx - 16, cy + compoundH / 2 + roadW + 5, 4, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(cx + 16, cy + compoundH / 2 + roadW + 5, 4, 0, Math.PI * 2);
        ctx.fill();

        // Gate label
        ctx.font = 'bold 10px sans-serif';
        ctx.fillStyle = COLORS.gate;
        ctx.textAlign = 'center';
        ctx.fillText('🚪 Main Entrance', cx, cy + compoundH / 2 + roadW + 24);
    }

    function drawPool(cx, cy) {
        // Pool
        ctx.beginPath();
        ctx.ellipse(cx, cy, 18, 12, 0, 0, Math.PI * 2);
        ctx.fillStyle = COLORS.pool;
        ctx.fill();
        ctx.strokeStyle = COLORS.poolLight;
        ctx.lineWidth = 2;
        ctx.stroke();

        // Water ripples
        ctx.beginPath();
        ctx.ellipse(cx, cy, 10, 6, 0, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(56,189,248,0.3)';
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.font = '9px sans-serif';
        ctx.fillStyle = COLORS.poolLight;
        ctx.textAlign = 'center';
        ctx.fillText('🏊 Pool', cx, cy + 4);
    }

    function drawPlayground(cx, cy) {
        const colors = ['#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
        for (let i = 0; i < 4; i++) {
            const angle = (i / 4) * Math.PI * 2;
            const x = cx + Math.cos(angle) * 8;
            const y = cy + Math.sin(angle) * 8;
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, Math.PI * 2);
            ctx.fillStyle = colors[i];
            ctx.fill();
        }

        ctx.font = '9px sans-serif';
        ctx.fillStyle = '#fbbf24';
        ctx.textAlign = 'center';
        ctx.fillText('🎮 Playground', cx, cy + 22);
    }

    function drawTree(x, y, size) {
        // Trunk
        ctx.fillStyle = '#78350f';
        ctx.fillRect(x - 1, y - 1, 2, 3);
        // Crown
        ctx.beginPath();
        ctx.arc(x, y - 3, size, 0, Math.PI * 2);
        ctx.fillStyle = COLORS.tree;
        ctx.fill();
        // Highlight
        ctx.beginPath();
        ctx.arc(x - 1, y - 4, size * 0.5, 0, Math.PI * 2);
        ctx.fillStyle = COLORS.treeLight;
        ctx.fill();
    }

    function drawBuilding(bx, by, bw, bh, building, buildingIndex) {
        const floors = building.floors || [];
        if (floors.length === 0) return;

        // Calculate unit grid inside building
        const allBuildingUnits = [];
        floors.forEach(floor => {
            (floor.units || []).forEach(unit => allBuildingUnits.push(unit));
        });

        // Count units by status
        const totalCount = allBuildingUnits.length;
        const availableCount = allBuildingUnits.filter(u => u.status === 'available').length;
        const reservedCount = allBuildingUnits.filter(u => u.status === 'reserved').length;
        const soldCount = allBuildingUnits.filter(u => u.status === 'sold').length;

        // Building shadow
        ctx.fillStyle = 'rgba(0,0,0,0.2)';
        roundRect(ctx, bx + 4, by + 4, bw, bh, 8);
        ctx.fill();

        // Building background
        ctx.fillStyle = COLORS.buildingBg;
        roundRect(ctx, bx, by, bw, bh, 8);
        ctx.fill();
        ctx.strokeStyle = COLORS.buildingBorder;
        ctx.lineWidth = 1.5;
        roundRect(ctx, bx, by, bw, bh, 8);
        ctx.stroke();

        // Building label
        ctx.font = 'bold 11px sans-serif';
        ctx.fillStyle = COLORS.buildingLabel;
        ctx.textAlign = 'center';
        ctx.fillText(building.name || building.code || `B${buildingIndex + 1}`, bx + bw / 2, by + 14);

        // Unit count badge (top-right corner)
        const badgeW = 38;
        const badgeH = 14;
        const badgeX = bx + bw - badgeW - 3;
        const badgeY = by + 3;

        // Badge background
        const badgeColor = availableCount > 0 ? '#10b981' : (soldCount === totalCount ? '#e11d48' : '#f59e0b');
        ctx.fillStyle = badgeColor;
        roundRect(ctx, badgeX, badgeY, badgeW, badgeH, 4);
        ctx.fill();

        // Badge text: available/total
        ctx.font = 'bold 8px sans-serif';
        ctx.fillStyle = '#fff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`${availableCount}/${totalCount}`, badgeX + badgeW / 2, badgeY + badgeH / 2);
        ctx.textBaseline = 'alphabetic';

        // Store building rect for hit testing
        buildingRects.push({ x: bx, y: by, w: bw, h: bh, building });

        if (allBuildingUnits.length === 0) return;

        const unitAreaX = bx + 6;
        const unitAreaY = by + 22;
        const unitAreaW = bw - 12;
        const unitAreaH = bh - 28;

        const cols = Math.min(Math.ceil(Math.sqrt(allBuildingUnits.length)), 6) || 1;
        const rows = Math.ceil(allBuildingUnits.length / cols) || 1;
        const uGap = 2;
        const uW = (unitAreaW - (cols - 1) * uGap) / cols;
        const uH = (unitAreaH - (rows - 1) * uGap) / rows;

        allBuildingUnits.forEach((unit, i) => {
            const col = i % cols;
            const row = Math.floor(i / cols);
            const ux = unitAreaX + col * (uW + uGap);
            const uy = unitAreaY + row * (uH + uGap);

            const isHovered = hoveredUnit && hoveredUnit.id === unit.id;
            const isSelected = selectedUnit && selectedUnit.id === unit.id;

            // Unit rectangle
            ctx.fillStyle = STATUS_COLORS[unit.status] || STATUS_COLORS.hidden;
            roundRect(ctx, ux, uy, uW, uH, 3);
            ctx.fill();

            // Hover/select overlay
            if (isHovered) {
                ctx.fillStyle = COLORS.unitHover;
                roundRect(ctx, ux, uy, uW, uH, 3);
                ctx.fill();
            }
            if (isSelected) {
                ctx.strokeStyle = '#fbbf24';
                ctx.lineWidth = 2;
                roundRect(ctx, ux, uy, uW, uH, 3);
                ctx.stroke();
            }

            // Unit number
            if (uW > 18 && uH > 14) {
                ctx.font = `bold ${Math.min(10, uW / 3)}px sans-serif`;
                ctx.fillStyle = COLORS.unitText;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(unit.number, ux + uW / 2, uy + uH / 2);
                ctx.textBaseline = 'alphabetic';
            }

            // Store for hit testing
            allUnits.push({ x: ux, y: uy, w: uW, h: uH, unit });
        });
    }

    function roundRect(ctx, x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + w - r, y);
        ctx.quadraticCurveTo(x + w, y, x + w, y + r);
        ctx.lineTo(x + w, y + h - r);
        ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        ctx.lineTo(x + r, y + h);
        ctx.quadraticCurveTo(x, y + h, x, y + h - r);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
    }

    // ===== Compound bounds (for mini-map) =====
    let compoundBounds = { x: 0, y: 0, w: 400, h: 300 };

    // ===== Main Draw =====
    function draw() {
        resize();
        const w = canvas.width / (window.devicePixelRatio || 1);
        const h = canvas.height / (window.devicePixelRatio || 1);

        ctx.save();
        ctx.translate(offsetX, offsetY);
        ctx.scale(scale, scale);

        allUnits = [];
        buildingRects = [];

        drawBackground(w / scale, h / scale);

        const cx = w / scale / 2;
        const cy = h / scale / 2;
        const buildings = data.buildings || [];
        const total = buildings.length;

        if (total === 0) {
            ctx.font = '14px sans-serif';
            ctx.fillStyle = COLORS.textDim;
            ctx.textAlign = 'center';
            ctx.fillText('No buildings found', cx, cy);
            ctx.restore();
            return;
        }

        // Compound dimensions
        const compoundW = Math.max(total * 40, 200);
        const compoundH = Math.max(120, compoundW * 0.5);

        // Store compound bounds for mini-map
        compoundBounds = {
            x: cx - compoundW / 2 - 40,
            y: cy - compoundH / 2 - 40,
            w: compoundW + 80,
            h: compoundH + 80,
        };

        drawRoad(cx, cy, compoundW, compoundH);

        // Central amenities
        drawPool(cx - 25, cy);
        drawPlayground(cx + 30, cy);

        // Trees around central area
        const treeRadius = Math.min(compoundW, compoundH) / 2 + 5;
        for (let i = 0; i < 16; i++) {
            const angle = (i / 16) * Math.PI * 2;
            const tx = cx + Math.cos(angle) * treeRadius;
            const ty = cy + Math.sin(angle) * treeRadius;
            drawTree(tx, ty, 4);
        }

        // Arrange buildings in U-shape
        const bCount = buildings.length;
        const positions = [];

        // Bottom row
        const bottomCount = Math.min(8, bCount);
        for (let i = 0; i < bottomCount; i++) {
            positions.push({ x: cx + (i - (bottomCount - 1) / 2) * 48, y: cy + compoundH / 2 - 10 });
        }
        // Right column
        const rightCount = Math.min(4, Math.max(0, bCount - 8));
        for (let i = 0; i < rightCount; i++) {
            positions.push({ x: cx + compoundW / 2 - 20, y: cy + (i - 1) * 48 });
        }
        // Top row
        const topCount = Math.min(8, Math.max(0, bCount - 12));
        for (let i = 0; i < topCount; i++) {
            positions.push({ x: cx + ((topCount - 1) / 2 - i) * 48, y: cy - compoundH / 2 + 10 });
        }
        // Left column
        const leftCount = Math.min(4, Math.max(0, bCount - 20));
        for (let i = 0; i < leftCount; i++) {
            positions.push({ x: cx - compoundW / 2 + 20, y: cy + (1 - i) * 48 });
        }
        // Inner (if more than 24)
        for (let i = 0; i < Math.max(0, bCount - 24); i++) {
            positions.push({ x: cx + (i % 4 - 1.5) * 55, y: cy + (Math.floor(i / 4) - 1) * 48 });
        }

        const bW = 42;
        const bH = 50;

        buildings.forEach((b, i) => {
            let pos;
            if (i < positions.length) {
                pos = positions[i];
            } else {
                const angle = (i / total) * Math.PI * 2;
                pos = { x: cx + Math.cos(angle) * 100, y: cy + Math.sin(angle) * 60 };
            }
            drawBuilding(pos.x - bW / 2, pos.y - bH / 2, bW, bH, b, i);
        });

        ctx.restore();

        // Draw mini-map (only when zoomed in)
        if (scale > 1.2) {
            drawMiniMap(w, h);
        }
    }

    // ===== Mini-Map =====
    let miniMapRect = null; // Store for click detection

    function drawMiniMap(canvasW, canvasH) {
        const mmW = 200;
        const mmH = 140;
        const mmPad = 14;
        const mmX = canvasW - mmW - mmPad;
        const mmY = canvasH - mmH - mmPad;

        miniMapRect = { x: mmX - 4, y: mmY - 28, w: mmW + 8, h: mmH + 32 };

        // Drop shadow
        ctx.shadowColor = 'rgba(0,0,0,0.4)';
        ctx.shadowBlur = 12;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 4;

        // Background
        ctx.fillStyle = 'rgba(10, 22, 16, 0.92)';
        roundRect(ctx, mmX - 4, mmY - 28, mmW + 8, mmH + 32, 10);
        ctx.fill();

        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;

        // Border
        ctx.strokeStyle = 'rgba(255,255,255,0.12)';
        ctx.lineWidth = 1;
        roundRect(ctx, mmX - 4, mmY - 28, mmW + 8, mmH + 32, 10);
        ctx.stroke();

        // Header bar
        ctx.fillStyle = 'rgba(255,255,255,0.05)';
        roundRect(ctx, mmX - 4, mmY - 28, mmW + 8, 22, [10, 10, 0, 0]);
        ctx.fill();

        // Header text
        ctx.font = 'bold 10px sans-serif';
        ctx.fillStyle = '#94a3b8';
        ctx.textAlign = 'left';
        ctx.fillText('🗺️ Overview', mmX + 4, mmY - 13);

        // Zoom percentage
        ctx.font = 'bold 9px sans-serif';
        ctx.fillStyle = '#fbbf24';
        ctx.textAlign = 'right';
        ctx.fillText(`${Math.round(scale * 100)}%`, mmX + mmW - 2, mmY - 13);

        // Map area
        ctx.save();
        ctx.beginPath();
        roundRect(ctx, mmX, mmY, mmW, mmH, 6);
        ctx.clip();

        // Dark map background
        ctx.fillStyle = '#0d1a12';
        ctx.fillRect(mmX, mmY, mmW, mmH);

        // Scale to fit compound in mini-map
        const scaleX = mmW / compoundBounds.w;
        const scaleY = mmH / compoundBounds.h;
        const mmScale = Math.min(scaleX, scaleY) * 0.85;
        const mmCx = mmX + mmW / 2;
        const mmCy = mmY + mmH / 2;
        const worldCx = compoundBounds.x + compoundBounds.w / 2;
        const worldCy = compoundBounds.y + compoundBounds.h / 2;

        ctx.translate(mmCx, mmCy);
        ctx.scale(mmScale, mmScale);
        ctx.translate(-worldCx, -worldCy);

        // Mini grass background
        ctx.fillStyle = COLORS.grass;
        roundRect(ctx, compoundBounds.x, compoundBounds.y, compoundBounds.w, compoundBounds.h, 8);
        ctx.fill();

        // Mini road
        ctx.beginPath();
        const roadR = Math.max(compoundBounds.w, compoundBounds.h) / 2 + 5;
        ctx.ellipse(worldCx, worldCy, roadR, roadR * 0.6, 0, 0, Math.PI * 2);
        ctx.strokeStyle = COLORS.road;
        ctx.lineWidth = 8;
        ctx.stroke();

        // Mini buildings with status dots
        buildingRects.forEach(br => {
            // Building shape
            ctx.fillStyle = '#3a5040';
            roundRect(ctx, br.x, br.y, br.w, br.h, 2);
            ctx.fill();
            ctx.strokeStyle = '#4a6a55';
            ctx.lineWidth = 0.5;
            roundRect(ctx, br.x, br.y, br.w, br.h, 2);
            ctx.stroke();

            // Status dot (center of building)
            const bUnits = [];
            const bFloors = br.building.floors || [];
            bFloors.forEach(f => (f.units || []).forEach(u => bUnits.push(u)));
            const avail = bUnits.filter(u => u.status === 'available').length;
            const total = bUnits.length;
            const dotColor = avail > 0 ? '#10b981' : (bUnits.every(u => u.status === 'sold') ? '#e11d48' : '#f59e0b');

            ctx.beginPath();
            ctx.arc(br.x + br.w / 2, br.y + br.h / 2, 3, 0, Math.PI * 2);
            ctx.fillStyle = dotColor;
            ctx.fill();
            ctx.strokeStyle = 'rgba(0,0,0,0.4)';
            ctx.lineWidth = 0.5;
            ctx.stroke();

            // Building name (tiny)
            ctx.font = 'bold 5px sans-serif';
            ctx.fillStyle = '#e2e8f0';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            const bName = br.building.name || br.building.code || '';
            ctx.fillText(bName, br.x + br.w / 2, br.y - 1);
            ctx.textBaseline = 'alphabetic';
        });

        // Mini pool
        ctx.beginPath();
        ctx.ellipse(worldCx - 15, worldCy, 8, 5, 0, 0, Math.PI * 2);
        ctx.fillStyle = COLORS.pool;
        ctx.fill();

        // Mini gate
        ctx.fillStyle = COLORS.gate;
        ctx.fillRect(worldCx - 6, worldCy + compoundBounds.h * 0.35, 12, 3);

        ctx.restore();

        // Viewport rectangle (what user is currently seeing)
        const viewLeft = mmX + mmW / 2 + (-offsetX / scale - worldCx) * mmScale;
        const viewTop = mmY + mmH / 2 + (-offsetY / scale - worldCy) * mmScale;
        const viewW = (canvasW / scale) * mmScale;
        const viewH = (canvasH / scale) * mmScale;

        // Viewport fill
        ctx.fillStyle = 'rgba(251,191,36,0.1)';
        ctx.fillRect(viewLeft, viewTop, viewW, viewH);

        // Viewport border with glow
        ctx.strokeStyle = '#fbbf24';
        ctx.lineWidth = 1.5;
        ctx.setLineDash([4, 3]);
        ctx.strokeRect(viewLeft, viewTop, viewW, viewH);
        ctx.setLineDash([]);

        // Corner dots on viewport
        const corners = [
            [viewLeft, viewTop],
            [viewLeft + viewW, viewTop],
            [viewLeft, viewTop + viewH],
            [viewLeft + viewW, viewTop + viewH],
        ];
        corners.forEach(([cx, cy]) => {
            ctx.beginPath();
            ctx.arc(cx, cy, 2.5, 0, Math.PI * 2);
            ctx.fillStyle = '#fbbf24';
            ctx.fill();
        });

        // Legend at bottom
        const legendY = mmY + mmH + 10;
        const legendItems = [
            { color: '#10b981', label: 'Available' },
            { color: '#f59e0b', label: 'Reserved' },
            { color: '#e11d48', label: 'Sold' },
        ];
        let legendX = mmX + 2;
        ctx.font = '7px sans-serif';
        legendItems.forEach(item => {
            ctx.beginPath();
            ctx.arc(legendX + 3, legendY, 3, 0, Math.PI * 2);
            ctx.fillStyle = item.color;
            ctx.fill();
            ctx.fillStyle = '#94a3b8';
            ctx.textAlign = 'left';
            ctx.fillText(item.label, legendX + 8, legendY + 2.5);
            legendX += ctx.measureText(item.label).width + 16;
        });
    }

    function miniMapClick(sx, sy) {
        if (!miniMapRect || scale <= 1.2) return false;
        const mr = miniMapRect;
        if (sx < mr.x || sx > mr.x + mr.w || sy < mr.y || sy > mr.y + mr.h) return false;

        // Map area starts at mmY (header is 22px)
        const mmW = 200, mmH = 140;
        const mmX = mr.x + 4;
        const mmY = mr.y + 28;
        const mmScale = Math.min(mmW / compoundBounds.w, mmH / compoundBounds.h) * 0.85;
        const mmCx = mmX + mmW / 2;
        const mmCy = mmY + mmH / 2;
        const worldCx = compoundBounds.x + compoundBounds.w / 2;
        const worldCy = compoundBounds.y + compoundBounds.h / 2;

        // Convert click position to world coordinates
        const clickWorldX = (sx - mmCx) / mmScale + worldCx;
        const clickWorldY = (sy - mmCy) / mmScale + worldCy;

        // Animate viewport to clicked point
        const rootRect = root.getBoundingClientRect();
        const canvasW = rootRect.width;
        const canvasH = rootRect.height;
        const targetOffX = canvasW / 2 - clickWorldX * scale;
        const targetOffY = canvasH / 2 - clickWorldY * scale;
        animateTo(targetOffX, targetOffY, scale);
        return true;
    }

    // ===== Interaction =====

    function screenToWorld(sx, sy) {
        return {
            x: (sx - offsetX) / scale,
            y: (sy - offsetY) / scale,
        };
    }

    function hitTest(sx, sy) {
        const { x, y } = screenToWorld(sx, sy);
        for (let i = allUnits.length - 1; i >= 0; i--) {
            const u = allUnits[i];
            if (x >= u.x && x <= u.x + u.w && y >= u.y && y <= u.y + u.h) {
                return u.unit;
            }
        }
        return null;
    }

    canvas.addEventListener('mousemove', (e) => {
        if (isDragging) {
            offsetX = lastOffset.x + (e.clientX - dragStart.x);
            offsetY = lastOffset.y + (e.clientY - dragStart.y);
            draw();
            return;
        }
        const unit = hitTest(e.offsetX, e.offsetY);
        if (unit !== hoveredUnit) {
            hoveredUnit = unit;
            canvas.style.cursor = unit ? 'pointer' : 'default';
            draw();
        }
    });

    canvas.addEventListener('mousedown', (e) => {
        isDragging = false;
        dragStart = { x: e.clientX, y: e.clientY };
        lastOffset = { x: offsetX, y: offsetY };
    });

    canvas.addEventListener('mousemove', () => {});

    canvas.addEventListener('mouseup', (e) => {
        const dx = Math.abs(e.clientX - dragStart.x);
        const dy = Math.abs(e.clientY - dragStart.y);
        if (dx < 4 && dy < 4) {
            // Check mini-map click first
            if (scale > 1.2 && miniMapClick(e.offsetX, e.offsetY)) {
                isDragging = false;
                return;
            }
            // Click — select unit
            const unit = hitTest(e.offsetX, e.offsetY);
            if (unit) {
                selectedUnit = selectedUnit?.id === unit.id ? null : unit;
            } else {
                selectedUnit = null;
            }
            onSelectUnit?.(selectedUnit);
            draw();
        }
        isDragging = false;
    });

    canvas.addEventListener('mouseleave', () => {
        if (hoveredUnit) {
            hoveredUnit = null;
            canvas.style.cursor = 'default';
            draw();
        }
    });

    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();
        const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
        const newScale = Math.max(0.3, Math.min(5, scale * zoomFactor));

        // Zoom towards cursor
        const mx = e.offsetX;
        const my = e.offsetY;
        offsetX = mx - (mx - offsetX) * (newScale / scale);
        offsetY = my - (my - offsetY) * (newScale / scale);

        scale = newScale;
        draw();
    }, { passive: false });

    // Touch support
    let lastTouchDist = 0;
    let lastTouchMid = { x: 0, y: 0 };

    canvas.addEventListener('touchstart', (e) => {
        if (e.touches.length === 1) {
            isDragging = true;
            dragStart = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            lastOffset = { x: offsetX, y: offsetY };
        } else if (e.touches.length === 2) {
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            lastTouchDist = Math.sqrt(dx * dx + dy * dy);
            lastTouchMid = {
                x: (e.touches[0].clientX + e.touches[1].clientX) / 2,
                y: (e.touches[0].clientY + e.touches[1].clientY) / 2,
            };
        }
        e.preventDefault();
    }, { passive: false });

    canvas.addEventListener('touchmove', (e) => {
        if (e.touches.length === 1 && isDragging) {
            offsetX = lastOffset.x + (e.touches[0].clientX - dragStart.x);
            offsetY = lastOffset.y + (e.touches[0].clientY - dragStart.y);
            draw();
        } else if (e.touches.length === 2) {
            const dx = e.touches[0].clientX - e.touches[1].clientX;
            const dy = e.touches[0].clientY - e.touches[1].clientY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const mid = {
                x: (e.touches[0].clientX + e.touches[1].clientX) / 2,
                y: (e.touches[0].clientY + e.touches[1].clientY) / 2,
            };

            if (lastTouchDist > 0) {
                const zoomFactor = dist / lastTouchDist;
                const newScale = Math.max(0.3, Math.min(5, scale * zoomFactor));
                offsetX = mid.x - (mid.x - offsetX) * (newScale / scale);
                offsetY = mid.y - (mid.y - offsetY) * (newScale / scale);
                scale = newScale;
                draw();
            }
            lastTouchDist = dist;
            lastTouchMid = mid;
        }
        e.preventDefault();
    }, { passive: false });

    canvas.addEventListener('touchend', (e) => {
        if (e.touches.length === 0) {
            // Tap to select
            if (isDragging) {
                const rect = canvas.getBoundingClientRect();
                const touch = e.changedTouches[0];
                const sx = touch.clientX - rect.left;
                const sy = touch.clientY - rect.top;
                const dx = Math.abs(touch.clientX - dragStart.x);
                const dy = Math.abs(touch.clientY - dragStart.y);
                if (dx < 10 && dy < 10) {
                    const unit = hitTest(sx, sy);
                    if (unit) {
                        selectedUnit = selectedUnit?.id === unit.id ? null : unit;
                    } else {
                        selectedUnit = null;
                    }
                    onSelectUnit?.(selectedUnit);
                    draw();
                }
            }
            isDragging = false;
            lastTouchDist = 0;
        }
    });

    // Initial draw
    draw();

    // Resize observer
    let resizeTimer;
    new ResizeObserver(() => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(draw, 100);
    }).observe(root);

    // Global API
    window.__drawProjectBuilding3D = draw;
    window.__toggleExploded3D = () => {};
    window.__isolateFloor3D = () => {};
    window.__isolateBuilding3D = (id) => {
        if (id !== null) {
            const br = buildingRects.find(b => b.building.id === id);
            if (br) {
                const cw = canvas.width / (window.devicePixelRatio || 1);
                const ch = canvas.height / (window.devicePixelRatio || 1);
                const targetScale = 2.5;
                const targetOffX = cw / 2 - (br.x + br.w / 2) * targetScale;
                const targetOffY = ch / 2 - (br.y + br.h / 2) * targetScale;
                animateTo(targetOffX, targetOffY, targetScale);
            }
        } else {
            animateTo(0, 0, 1);
        }
    };
    window.__resetCamera3D = () => {
        animateTo(0, 0, 1);
    };
}
