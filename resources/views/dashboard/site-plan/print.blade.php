@php
    $labels = ['available' => 'متاح', 'reserved' => 'محجوز', 'sold' => 'تم البيع', 'hidden' => 'مخفي'];
    $counts = collect($statusColors)->mapWithKeys(fn ($color, $key) => [$key => $allUnits->where('status', $key)->count()]);
    $totalArea = (float) $allUnits->sum(fn ($unit) => (float) $unit['area']);
    $allFloors = $buildings->flatMap(fn ($building) => $building['floors']->map(fn ($floor) => [
        'number' => (int) $floor->number, 'name' => $floor->name,
    ]))->sortBy('number')->unique('number')->values();
    $floorName = fn ($floor) => filled($floor['name']) ? $floor['name'] : ((int) $floor['number'] === 0 ? 'الدور الأرضي' : 'الدور '.$floor['number']);
    $formatArea = function ($area) {
        $area = (float) $area;
        return floor($area) === $area ? number_format($area, 0, '.', '') : rtrim(rtrim(number_format($area, 2, '.', ''), '0'), '.');
    };
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>المخطط العام — {{ $project->name }}</title>
    <style>
        body { margin: 0; color: #172033; background: #fff; font-family: tajawal, sans-serif; font-size: 7.5pt; direction: rtl; }
        table { border-collapse: collapse; }
        .masthead { width: 100%; background: #111c32; color: #fff; }
        .masthead td { padding: 3mm 4mm; vertical-align: middle; border-bottom: 1.2mm solid #0d9488; }
        .title { font-size: 16pt; font-weight: bold; line-height: 1.15; }
        .project { margin-top: 1mm; color: #a7f3d0; font-size: 9pt; }
        .document-meta { text-align: left; color: #cbd5e1; font-size: 7pt; line-height: 1.7; }
        .document-tag { display: inline-block; padding: 1mm 3mm; background: #0f766e; color: #fff; font-weight: bold; }
        .stats { width: 100%; margin: 2.2mm 0 1.8mm; border: .3mm solid #dbe3ee; }
        .stats td { padding: 1.5mm 2mm; text-align: center; border-left: .25mm solid #e5eaf1; background: #f8fafc; }
        .stats td:last-child { border-left: 0; }
        .stat-number { font-size: 10pt; font-weight: bold; color: #111c32; }
        .stat-label { color: #68758a; font-size: 6pt; margin-top: .4mm; }
        .legend { width: 100%; margin-bottom: 2mm; }
        .legend td { text-align: center; color: #475569; font-size: 7pt; }
        .swatch { display: inline-block; width: 3.2mm; height: 3.2mm; margin-left: 1mm; vertical-align: -1mm; border: .2mm solid #cbd5e1; }
        .legend strong { color: #0f172a; font-size: 8pt; }
        .floor { margin-bottom: 3mm; }
        .floor-head { width: 100%; background: #e9eef6; border: .25mm solid #d7dee9; }
        .floor-head td { padding: 1.6mm 3mm; }
        .floor-title { color: #14213a; font-size: 9.5pt; font-weight: bold; border-right: 1.2mm solid #0f766e; }
        .floor-total { color: #64748b; font-size: 6.5pt; text-align: left; }
        .landmarks { width: 96%; margin: .7mm auto .4mm; direction: ltr; }
        .landmarks td { color: #65a30d; font-size: 5.8pt; }
        .buildings { width: 96%; margin: 0 auto; border-collapse: separate; border-spacing: 1.2mm 0; direction: ltr; }
        .buildings > tbody > tr > td { vertical-align: top; direction: ltr; }
        .group-gap { width: 5mm; }
        .building { padding: .8mm; border: .25mm solid #cbd5e1; background: #f8fafc; }
        .building-code { padding: .55mm 1mm; text-align: center; background: #24499a; color: #fff; font-size: 6.3pt; font-weight: bold; white-space: nowrap; }
        .building-code span { color: #dbeafe; font-size: 5.2pt; font-weight: normal; }
        .units { width: 100%; margin-top: .65mm; border-collapse: separate; border-spacing: .5mm; }
        .units td { height: 11mm; padding: .5mm; text-align: center; vertical-align: middle; border: .2mm solid #667085; }
        .unit-area { font-size: 8pt; line-height: 1.05; font-weight: bold; }
        .unit-detail { margin-top: .35mm; font-size: 4.2pt; line-height: 1; font-weight: bold; text-transform: uppercase; }
        .unit-slot { display: inline-block; margin-top: .3mm; padding: 0 .7mm; border: .2mm solid #dc2626; background: #fff; color: #dc2626; font-size: 4.5pt; line-height: 1.5; }
        .available { background: #18ad58; color: #fff; }
        .reserved { background: #f5a400; color: #172033; }
        .sold { background: #df3434; color: #fff; }
        .hidden { background: #64748b; color: #fff; }
        .footer-rule { margin-top: 1.5mm; border-top: .3mm solid #dbe3ee; }
        .footer { width: 100%; margin-top: 1.3mm; color: #718096; font-size: 5.8pt; }
        .footer td:last-child { text-align: left; }
    </style>
</head>
<body>
    <table class="masthead"><tr>
        <td style="width:65%"><div class="title">المخطط العام للوحدات</div><div class="project">{{ $project->name }}</div></td>
        <td style="width:18%; text-align:center"><span class="document-tag">تقرير توافر الوحدات</span></td>
        <td class="document-meta" style="width:17%">مقاس الطباعة: A3 أفقي<br>{{ now()->format('Y/m/d — H:i') }}</td>
    </tr></table>
    <table class="stats"><tr>
        <td><div class="stat-number">{{ $buildings->count() }}</div><div class="stat-label">عدد المباني</div></td>
        <td><div class="stat-number">{{ $allFloors->count() }}</div><div class="stat-label">عدد الأدوار</div></td>
        <td><div class="stat-number">{{ $allUnits->count() }}</div><div class="stat-label">إجمالي الوحدات</div></td>
        <td><div class="stat-number">{{ number_format($totalArea) }} م²</div><div class="stat-label">إجمالي المساحات</div></td>
        <td><div class="stat-number" style="color:#15803d">{{ $counts['available'] ?? 0 }}</div><div class="stat-label">متاح</div></td>
        <td><div class="stat-number" style="color:#b77900">{{ $counts['reserved'] ?? 0 }}</div><div class="stat-label">محجوز</div></td>
        <td><div class="stat-number" style="color:#c62828">{{ $counts['sold'] ?? 0 }}</div><div class="stat-label">تم البيع</div></td>
    </tr></table>
    <table class="legend"><tr>
        @foreach($statusColors as $key => $color)
            <td><span class="swatch" style="background:{{ $color['bg'] }}"></span><strong>{{ $counts[$key] ?? 0 }}</strong> {{ $labels[$key] ?? $color['label'] }}</td>
        @endforeach
        <td>المساحة داخل الوحدة بالمتر المربع</td><td>الرقم الصغير = موضع الوحدة</td>
    </tr></table>

    @foreach($allFloors as $floorInfo)
        @php
            $floorNumber = $floorInfo['number']; $floorUnits = 0;
            foreach ($buildings as $building) {
                $floorId = $building['floors']->firstWhere('number', $floorNumber)?->id;
                $floorUnits += $floorId ? ($building['units_by_floor'][$floorId] ?? collect())->count() : 0;
            }
        @endphp
        @if($floorUnits)
            <div class="floor">
                <table class="floor-head"><tr><td class="floor-title">{{ $floorName($floorInfo) }}</td><td class="floor-total">{{ $floorUnits }} وحدة</td></tr></table>
                <table class="landmarks"><tr><td style="width:33%;text-align:left">جامعة سفنكس</td><td style="width:34%;text-align:center">اللاند سكيب</td><td style="width:33%;text-align:right">وادي دجلة</td></tr></table>
                <table class="buildings" align="center" dir="ltr"><tr>
                    @php $previousGroup = null; @endphp
                    @foreach($buildings as $building)
                        @php
                            $floorId = $building['floors']->firstWhere('number', $floorNumber)?->id;
                            $units = $floorId ? ($building['units_by_floor'][$floorId] ?? collect()) : collect();
                        @endphp
                        @if($units->isNotEmpty())
                            @php
                                $units = $units->sortBy(fn ($unit) => [(int) ($unit['grid_row'] ?? 0), (int) ($unit['grid_col'] ?? $unit['position_in_grid'] ?? 0)])->values();
                                $columns = $units->count() <= 4 ? 2 : min(4, (int) ceil(sqrt($units->count())));
                            @endphp
                            @if($previousGroup !== null && $previousGroup !== $building['layout_group'])<td class="group-gap">&nbsp;</td>@endif
                            <td><div class="building">
                                <div class="building-code">{{ $building['code'] }} <span>({{ $units->count() }})</span></div>
                                <table class="units">
                                    @foreach($units->chunk($columns) as $row)<tr>
                                        @foreach($row as $unit)
                                            @php
                                                $position = (int) ($unit['position_in_grid'] ?: $loop->iteration);
                                                $isDuplex = str_contains(strtolower((string) $unit['unit_type']), 'duplex');
                                                $hasGarden = (float) $unit['garden_area'] > 0;
                                            @endphp
                                            <td class="{{ array_key_exists($unit['status'], $statusColors) ? $unit['status'] : 'hidden' }}">
                                                <div class="unit-area">{{ $formatArea($unit['area']) }}</div>
                                                @if($hasGarden)<div class="unit-detail">GARDEN</div>
                                                @elseif($isDuplex)<div class="unit-detail">DUPLEX</div>
                                                @else<span class="unit-slot">{{ $position }}</span>@endif
                                            </td>
                                        @endforeach
                                        @for($i = $row->count(); $i < $columns; $i++)<td style="border:0;background:transparent">&nbsp;</td>@endfor
                                    </tr>@endforeach
                                </table>
                            </div></td>
                            @php $previousGroup = $building['layout_group']; @endphp
                        @endif
                    @endforeach
                </tr></table>
            </div>
        @endif
    @endforeach
    <div class="footer-rule"></div>
    <table class="footer"><tr><td>{{ $project->name }} — تقرير داخلي لحالة الوحدات</td><td>تم الإنشاء: {{ now()->format('Y/m/d H:i') }}</td></tr></table>
</body>
</html>
