import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { initPwa } from './pwa';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
initPwa();

// Theme switcher
function updateThemeMeta(isDark) {
  const themeColor = document.querySelector('meta[name="theme-color"]');
  if (themeColor) {
    themeColor.setAttribute('content', isDark ? '#0b1120' : '#f8fafc');
  }
}

window.theme = {
  init() {
    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored ? stored === 'dark' : prefersDark;
    document.documentElement.classList.toggle('dark', isDark);
    updateThemeMeta(isDark);
  },
  toggle() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeMeta(isDark);
  },
};

window.theme.init();

// Toast notifications
function createToastElement(message, type) {
  const container = document.getElementById('app-toast-container');
  if (!container) return;

  const item = document.createElement('div');
  item.className = `app-toast__item app-toast__item--${type}`;
  item.setAttribute('role', 'status');

  const icons = {
    success: '<svg class="app-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    info: '<svg class="app-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M12 16v-4M12 8h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    warning: '<svg class="app-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke-width="1.8"/><path d="M12 9v4M12 17h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    danger: '<svg class="app-toast__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><path d="M15 9l-6 6M9 9l6 6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
  };

  item.innerHTML = `
    ${icons[type] || icons.info}
    <span class="app-toast__message">${message}</span>
    <button type="button" class="app-toast__close" aria-label="Close">&times;</button>
  `;

  item.querySelector('button').addEventListener('click', () => removeToast(item));

  container.appendChild(item);

  requestAnimationFrame(() => {
    item.style.transform = 'translateY(0)';
    item.style.opacity = '1';
  });

  const timeout = setTimeout(() => removeToast(item), 5000);
  item.dataset.timeout = timeout;
}

function removeToast(item) {
  if (item.dataset.timeout) clearTimeout(parseInt(item.dataset.timeout));
  item.style.opacity = '0';
  item.style.transform = 'translateX(100%)';
  if (document.documentElement.getAttribute('dir') === 'rtl') {
    item.style.transform = 'translateX(-100%)';
  }
  setTimeout(() => item.remove(), 300);
}

window.toast = {
  success(message) { createToastElement(message, 'success'); },
  info(message) { createToastElement(message, 'info'); },
  warning(message) { createToastElement(message, 'warning'); },
  danger(message) { createToastElement(message, 'danger'); },
};

if (window.__flash?.message) {
  const { message, type = 'info' } = window.__flash;
  (window.toast[type] || window.toast.info)(message);
  delete window.__flash;
}

// Generic confirmation modal
window.confirmAction = function (title, message, onConfirm, confirmText = 'Confirm') {
  const modal = document.getElementById('app-confirm-modal');
  if (!modal) return onConfirm ? onConfirm() : null;

  modal.querySelector('[data-modal-title]').textContent = title;
  modal.querySelector('[data-modal-body]').textContent = message;
  modal.querySelector('[data-modal-confirm]').textContent = confirmText;

  const confirmBtn = modal.querySelector('[data-modal-confirm]');

  const cleanup = () => {
    modal.classList.add('hidden');
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
  };

  const newConfirm = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);
  newConfirm.addEventListener('click', () => {
    if (typeof onConfirm === 'function') onConfirm();
    cleanup();
  });

  modal.querySelector('[data-modal-cancel]').addEventListener('click', cleanup, { once: true });
  modal.querySelector('[data-modal-close]').addEventListener('click', cleanup, { once: true });

  modal.classList.remove('hidden');
};

// Keep the user dropdown anchored to its button and inside the viewport frame.
// The header's backdrop-filter makes it the containing block for fixed children,
// so coordinates are computed relative to the header element.
window.positionUserMenu = function (btn, panel) {
  const header = btn.closest('header');
  if (!header) return;
  const hr = header.getBoundingClientRect();
  const br = btn.getBoundingClientRect();
  const pw = panel.offsetWidth || 224; // w-56 fallback
  panel.style.top = `${br.bottom - hr.top + 8}px`;
  let left = br.right - hr.left - pw;
  left = Math.max(8, Math.min(left, hr.width - pw - 8));
  panel.style.left = `${left}px`;
};

// Ctrl+S / Cmd+S saves the active form instead of the browser's "Save page" dialog.
// Only forms marked with data-save-shortcut are targeted (e.g. project/unit edit
// forms). Delete forms and search forms are always ignored.
document.addEventListener('keydown', (e) => {
  if (e.shiftKey) return;
  if (!(e.ctrlKey || e.metaKey) || e.key.toLowerCase() !== 's') return;

  const saveForm = (form) => {
    if (!form) return;
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitBtn) {
      submitBtn.click();
    } else {
      try {
        form.requestSubmit();
      } catch {
        form.submit();
      }
    }
  };

  let form = document.activeElement && document.activeElement.closest
    ? document.activeElement.closest('form[data-save-shortcut]')
    : null;

  if (form) {
    saveForm(form);
    return;
  }

  const visibleShortcutForm = Array.from(document.querySelectorAll('form[data-save-shortcut]')).find((f) => {
    const rect = f.getBoundingClientRect();
    return rect.width > 0 && rect.height > 0;
  });

  if (visibleShortcutForm) {
    saveForm(visibleShortcutForm);
  }
});
