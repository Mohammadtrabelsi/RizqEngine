import './bootstrap.js';

const THEME_COOKIE = 'theme';

function writeThemeCookie(value) {
    document.cookie = `${THEME_COOKIE}=${value};path=/;max-age=31536000;samesite=lax`;
}

/*
 * Lightweight replacements for the interactive widgets that the CoreUI /
 * Bootstrap 4 JavaScript bundle used to provide. The markup still uses the
 * familiar `data-toggle` / `data-dismiss` / `.show` API, so these handlers
 * keep every existing view working without the Bootstrap dependency.
 */

// --- Dropdowns -------------------------------------------------------------
function closeAllDropdowns(except) {
    document.querySelectorAll('.dropdown.show, .c-header-nav-item.show, .app-topnav-item.show')
        .forEach((parent) => {
            if (parent === except) return;
            parent.classList.remove('show');
            const menu = parent.querySelector('.dropdown-menu');
            if (menu) menu.classList.remove('show');
        });
}

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-toggle="dropdown"]');
    if (toggle) {
        event.preventDefault();
        const parent = toggle.closest('.dropdown, .c-header-nav-item, .app-topnav-item');
        if (!parent) return;
        const isOpen = parent.classList.contains('show');
        closeAllDropdowns(isOpen ? null : parent);
        parent.classList.toggle('show', !isOpen);
        const menu = parent.querySelector('.dropdown-menu');
        if (menu) menu.classList.toggle('show', !isOpen);
        return;
    }

    // Clicking inside an open menu should not close it (unless it's a link).
    if (event.target.closest('.dropdown-menu') && !event.target.closest('a, button')) {
        return;
    }
    closeAllDropdowns(null);
});

// --- Collapse (e.g. the mobile navigation) ---------------------------------
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-toggle="collapse"]');
    if (!trigger) return;
    event.preventDefault();
    const selector = trigger.getAttribute('data-target') || trigger.getAttribute('href');
    if (!selector) return;
    const target = document.querySelector(selector);
    if (!target) return;
    const willShow = !target.classList.contains('show');
    target.classList.toggle('show', willShow);
    trigger.setAttribute('aria-expanded', String(willShow));
});

// --- Sidebar submenu toggle ------------------------------------------------
// Expands/collapses a collapsible sidebar section. The button carries
// `.is-open` (drives the caret) and its sibling `.app-sidebar-sublist` carries
// `.is-open` (drives visibility). Server-rendered state opens the section that
// contains the current route.
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-toggle="submenu"]');
    if (!trigger) return;
    event.preventDefault();
    const sublist = trigger.parentElement
        ? trigger.parentElement.querySelector('.app-sidebar-sublist')
        : null;
    const willOpen = !trigger.classList.contains('is-open');
    trigger.classList.toggle('is-open', willOpen);
    trigger.setAttribute('aria-expanded', String(willOpen));
    if (sublist) sublist.classList.toggle('is-open', willOpen);
});

// --- Price detail toggle (cart) --------------------------------------------
// Replaces the former Alpine `x-data`/`x-show` toggle: clicking (or pressing
// Enter/Space on) the net unit price reveals the price breakdown.
function togglePriceDetail(summary) {
    const wrapper = summary.closest('span');
    if (!wrapper) return;
    const detail = wrapper.querySelector('.js-price-detail');
    if (!detail) return;
    const willShow = detail.hasAttribute('hidden');
    detail.toggleAttribute('hidden', !willShow);
    summary.toggleAttribute('hidden', willShow);
}
document.addEventListener('click', (event) => {
    const summary = event.target.closest('[data-toggle="price-detail"]');
    if (summary) togglePriceDetail(summary);
});
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' && event.key !== ' ') return;
    const summary = event.target.closest('[data-toggle="price-detail"]');
    if (!summary) return;
    event.preventDefault();
    togglePriceDetail(summary);
});

// --- Modals ----------------------------------------------------------------
function openModal(modal) {
    if (!modal) return;
    modal.classList.add('show');
    modal.style.display = 'block';
    modal.removeAttribute('aria-hidden');
    document.body.classList.add('modal-open');
    if (!document.querySelector('.modal-backdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop show';
        document.body.appendChild(backdrop);
    }
}

function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) backdrop.remove();
}

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-toggle="modal"]');
    if (opener) {
        event.preventDefault();
        const selector = opener.getAttribute('data-target');
        if (selector) openModal(document.querySelector(selector));
        return;
    }
    const dismiss = event.target.closest('[data-dismiss="modal"]');
    if (dismiss) {
        event.preventDefault();
        closeModal(dismiss.closest('.modal'));
        return;
    }
    // Click on the backdrop area of a modal.
    if (event.target.classList.contains('modal') && event.target.classList.contains('show')) {
        closeModal(event.target);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeModal(document.querySelector('.modal.show'));
        closeAllDropdowns(null);
    }
});

// --- Submit-a-referenced-form buttons --------------------------------------
// Replaces inline `onclick="…getElementById('id').submit()"` handlers. A
// control carrying `data-submit-form="<form id>"` submits that hidden form
// (used for the delete/confirm/convert row actions and the logout link).
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-submit-form]');
    if (!trigger) return;
    event.preventDefault();
    const form = document.getElementById(trigger.getAttribute('data-submit-form'));
    if (form) form.submit();
});

// --- Language switcher dropdown --------------------------------------------
// Toggles the .is-open state on the language switcher and closes any other
// open one. Replaces the inline <script> in the language-switcher partial.
document.addEventListener('click', (event) => {
    const toggle = event.target.closest('.language-switcher__toggle');
    document.querySelectorAll('.language-switcher.is-open').forEach((el) => {
        if (!toggle || toggle.parentElement !== el) el.classList.remove('is-open');
    });
    if (toggle) {
        const open = toggle.parentElement.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
});

// --- Dismissible alerts ----------------------------------------------------
document.addEventListener('click', (event) => {
    const closer = event.target.closest('[data-dismiss="alert"]');
    if (!closer) return;
    const alert = closer.closest('.alert');
    if (alert) alert.remove();
});

// --- jQuery plugin shims ---------------------------------------------------
// Some inline view scripts still call the Bootstrap jQuery API (e.g.
// `$('#checkoutModal').modal('show')`). Bridge those calls to the vanilla
// handlers above so the markup keeps working without the Bootstrap bundle.
if (window.jQuery) {
    const $ = window.jQuery;
    $.fn.modal = function (action) {
        this.each((_, el) => {
            if (action === 'hide') closeModal(el);
            else if (action === 'toggle') {
                el.classList.contains('show') ? closeModal(el) : openModal(el);
            } else openModal(el);
        });
        return this;
    };
    // Tooltips were decorative only; expose a no-op to avoid errors.
    $.fn.tooltip = function () { return this; };
    $.fn.dropdown = function (action) {
        this.each((_, el) => {
            const parent = el.closest('.dropdown, .c-header-nav-item, .app-topnav-item');
            if (!parent) return;
            const show = action === 'show' || (action !== 'hide' && !parent.classList.contains('show'));
            parent.classList.toggle('show', show);
            const menu = parent.querySelector('.dropdown-menu');
            if (menu) menu.classList.toggle('show', show);
        });
        return this;
    };
}

// --- Force the light theme (parity with the previous behaviour) ------------
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.remove('c-dark-theme');
    writeThemeCookie('light');

    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            const form = document.getElementById('logout-form');
            if (form) form.submit();
        });
    }
});
