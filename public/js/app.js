/* ==========================================================================
   DocuSecure – shared frontend behaviour (no dependencies)
   Hooks are declared with data-attributes in Blade so pages stay declarative:
     data-sidebar-toggle         collapse (desktop) / open (tablet & mobile) sidebar
     data-dropdown-trigger       toggle the closest .dropdown
     data-modal-open="#id"       open a modal      data-modal-close  close it
     data-toast="message"        show a toast on click (data-toast-type optional)
     data-filter-input           live-filter rows: data-filter-target="#table-body"
   Public API: window.DocuSecure.toast(message, type)
   ========================================================================== */
(function () {
    "use strict";

    var app = document.querySelector(".app");
    var STORAGE_KEY = "docusecure.sidebarCollapsed";
    var MOBILE_QUERY = "(max-width: 1024px)";

    function safeGet(key) {
        try { return window.localStorage.getItem(key); } catch (e) { return null; }
    }
    function safeSet(key, value) {
        try { window.localStorage.setItem(key, value); } catch (e) { /* storage unavailable */ }
    }

    /* ---- Sidebar ---------------------------------------------------------- */
    function initSidebar() {
        if (!app) return;
        if (safeGet(STORAGE_KEY) === "1") app.classList.add("is-collapsed");

        document.querySelectorAll("[data-sidebar-toggle]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                if (window.matchMedia(MOBILE_QUERY).matches) {
                    app.classList.toggle("is-sidebar-open");
                } else {
                    var collapsed = app.classList.toggle("is-collapsed");
                    safeSet(STORAGE_KEY, collapsed ? "1" : "0");
                    btn.setAttribute("aria-expanded", String(!collapsed));
                }
            });
        });

        var backdrop = document.querySelector(".sidebar-backdrop");
        if (backdrop) {
            backdrop.addEventListener("click", function () {
                app.classList.remove("is-sidebar-open");
            });
        }
    }

    /* ---- Dropdowns -------------------------------------------------------- */
    function closeDropdowns(except) {
        document.querySelectorAll(".dropdown.is-open").forEach(function (dd) {
            if (dd !== except) {
                dd.classList.remove("is-open");
                var trigger = dd.querySelector("[data-dropdown-trigger]");
                if (trigger) trigger.setAttribute("aria-expanded", "false");
            }
        });
    }

    function initDropdowns() {
        document.querySelectorAll("[data-dropdown-trigger]").forEach(function (trigger) {
            trigger.addEventListener("click", function (event) {
                event.stopPropagation();
                var dropdown = trigger.closest(".dropdown");
                var willOpen = !dropdown.classList.contains("is-open");
                closeDropdowns(willOpen ? dropdown : null);
                dropdown.classList.toggle("is-open", willOpen);
                trigger.setAttribute("aria-expanded", String(willOpen));
            });
        });
        document.addEventListener("click", function (event) {
            if (!event.target.closest(".dropdown__menu")) closeDropdowns(null);
        });
    }

    /* ---- Modals ----------------------------------------------------------- */
    var lastFocused = null;

    function openModal(modal) {
        if (!modal) return;
        lastFocused = document.activeElement;
        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
        var focusable = modal.querySelector("input, select, textarea, button:not([data-modal-close])") ||
                        modal.querySelector("button");
        if (focusable) focusable.focus();
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
        if (lastFocused) lastFocused.focus();
    }

    function initModals() {
        document.addEventListener("click", function (event) {
            var opener = event.target.closest("[data-modal-open]");
            if (opener) {
                closeDropdowns(null);
                openModal(document.querySelector(opener.getAttribute("data-modal-open")));
                return;
            }
            if (event.target.closest("[data-modal-close]")) {
                closeModal(event.target.closest(".modal"));
            }
        });
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                document.querySelectorAll(".modal.is-open").forEach(closeModal);
                closeDropdowns(null);
                if (app) app.classList.remove("is-sidebar-open");
            }
        });
    }

    /* ---- Toasts ----------------------------------------------------------- */
    function toast(message, type) {
        var stack = document.getElementById("toast-stack");
        if (!stack) return;
        var el = document.createElement("div");
        el.className = "toast" + (type ? " toast--" + type : "");
        el.setAttribute("role", "status");
        el.textContent = message;
        stack.appendChild(el);
        window.setTimeout(function () {
            el.style.opacity = "0";
            el.style.transition = "opacity 0.3s";
            window.setTimeout(function () { el.remove(); }, 300);
        }, 3500);
    }

    function initToastTriggers() {
        document.addEventListener("click", function (event) {
            var trigger = event.target.closest("[data-toast]");
            if (!trigger) return;
            toast(trigger.getAttribute("data-toast"), trigger.getAttribute("data-toast-type") || "success");
            if (trigger.hasAttribute("data-modal-close")) return; // modal handler closes it
        });
    }

    /* ---- Notifications ---------------------------------------------------- */
    function initNotifications() {
        var markAll = document.querySelector("[data-notif-mark-read]");
        if (!markAll) return;
        markAll.addEventListener("click", function () {
            document.querySelectorAll(".notif-item.is-unread").forEach(function (item) {
                item.classList.remove("is-unread");
            });
            var dot = document.querySelector(".icon-btn__dot");
            if (dot) dot.remove();
            toast("All notifications marked as read.", "success");
        });
    }

    /* ---- Live row filter (search over mock data already in the DOM) ------- */
    function initFilters() {
        document.querySelectorAll("[data-filter-input]").forEach(function (input) {
            var target = document.querySelector(input.getAttribute("data-filter-target"));
            if (!target) return;
            var emptyRow = target.parentElement.querySelector("[data-filter-empty]");

            input.addEventListener("input", function () {
                var query = input.value.trim().toLowerCase();
                var visible = 0;
                target.querySelectorAll("[data-filter-row]").forEach(function (row) {
                    var match = row.textContent.toLowerCase().indexOf(query) !== -1;
                    row.hidden = !match;
                    if (match) visible++;
                });
                if (emptyRow) emptyRow.hidden = visible !== 0;
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initSidebar();
        initDropdowns();
        initModals();
        initToastTriggers();
        initNotifications();
        initFilters();
    });

    window.DocuSecure = { toast: toast, openModal: openModal, closeModal: closeModal };
})();
