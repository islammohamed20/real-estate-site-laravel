/**
 * Live Refresh - Smart polling for dashboard sections.
 * Polls /live-status/poll and updates UI elements with data-live attributes.
 * - Stops when tab is hidden (saves server load)
 * - Stops after 30 min of inactivity
 * - Rate-limited: min 3s between requests
 * - Live indicator: green dot near notification bell
 * - Tab title flash when new data arrives
 */
(function () {
  'use strict';

  const POLL_URL = '/real-statement-control/live-status/poll';
  const INTERVALS = {
    whatsapp: 30000,
    notifications: 60000,
    leads: 120000,
    stats: 180000,
  };
  const MIN_GAP = 3000;
  const INACTIVITY_TIMEOUT = 30 * 60 * 1000;

  let lastPoll = 0;
  let lastData = {};
  let timers = {};
  let active = true;
  let lastActivity = Date.now();
  let origTitle = document.title;

  // --- Helpers ---
  function isVisible() {
    return document.visibilityState === 'visible';
  }

  function isInactive() {
    return Date.now() - lastActivity > INACTIVITY_TIMEOUT;
  }

  function rateLimit() {
    const now = Date.now();
    if (now - lastPoll < MIN_GAP) return false;
    lastPoll = now;
    return true;
  }

  // --- Activity tracking ---
  function markActive() {
    lastActivity = Date.now();
    if (!active) {
      active = true;
      startPolling();
    }
  }

  ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(function (evt) {
    document.addEventListener(evt, markActive, { passive: true });
  });

  document.addEventListener('visibilitychange', function () {
    if (isVisible()) {
      markActive();
      pollNow();
    }
  });

  // --- Polling ---
  async function fetchStatus() {
    try {
      const res = await fetch(POLL_URL, { credentials: 'same-origin' });
      if (!res.ok) return null;
      return await res.json();
    } catch (e) {
      return null;
    }
  }

  async function pollNow() {
    if (!isVisible() || isInactive() || !rateLimit()) return;
    const data = await fetchStatus();
    if (!data) return;
    processUpdate(data);
  }

  function processUpdate(data) {
    // WhatsApp count
    var newWaba = data.whatsapp_unread || 0;
    var oldWaba = lastData.whatsapp_unread || 0;
    if (newWaba !== oldWaba) {
      updateElements('whatsapp_unread', newWaba);
      if (newWaba > oldWaba) flashTab();
    }

    // Notifications
    var newNotif = data.notifications_unread || 0;
    var oldNotif = lastData.notifications_unread || 0;
    if (newNotif !== oldNotif) {
      updateElements('notifications_unread', newNotif);
      if (newNotif > oldNotif) flashTab();
    }

    // Leads
    updateElements('leads_count', data.leads_count || 0);

    // Stats
    updateElements('total_customers', data.total_customers || 0);
    updateElements('total_plans', data.total_plans || 0);
    updateElements('total_revenue', data.total_revenue || 0);

    // Live indicator
    showLiveDot(newWaba > 0 || newNotif > 0);

    lastData = data;
  }

  function updateElements(key, value) {
    document.querySelectorAll('[data-live="' + key + '"]').forEach(function (el) {
      var text = typeof value === 'number' ? value.toLocaleString() : value;
      if (el.textContent !== text) {
        el.textContent = text;
        el.classList.add('live-flash');
        setTimeout(function () { el.classList.remove('live-flash'); }, 1000);
      }
    });
  }

  function showLiveDot(hasNew) {
    var dot = document.getElementById('live-dot');
    if (!dot) {
      var bell = document.querySelector('[data-live-dot]');
      if (bell) {
        dot = document.createElement('span');
        dot.id = 'live-dot';
        dot.className = 'absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-emerald-400';
        dot.style.display = 'none';
        bell.style.position = 'relative';
        bell.appendChild(dot);
      }
    }
    if (dot) dot.style.display = hasNew ? 'block' : 'none';
  }

  function flashTab() {
    var count = (lastData.whatsapp_unread || 0) + (lastData.notifications_unread || 0);
    if (count > 0) {
      document.title = '(' + count + ') ' + origTitle;
      setTimeout(function () { document.title = origTitle; }, 5000);
    }
  }

  // --- Start/Stop ---
  function startPolling() {
    stopPolling();
    var categories = Object.keys(INTERVALS);
    categories.forEach(function (cat) {
      timers[cat] = setInterval(function () {
        if (isVisible() && !isInactive()) pollNow();
      }, INTERVALS[cat]);
    });
  }

  function stopPolling() {
    Object.keys(timers).forEach(function (cat) {
      clearInterval(timers[cat]);
    });
    timers = {};
  }

  // Initial poll after 2s
  setTimeout(pollNow, 2000);
  startPolling();
})();
