/* ==========================================================================
   DocuSecure – form helpers shared by every page (extends window.DocuSecure)
     DocuSecure.setFieldError(input, message)   show an inline error under a .field
     DocuSecure.clearFieldError(input)
     DocuSecure.clearFormErrors(form)
     DocuSecure.initialsOf(name)                "Dr. Emily Davis" -> "ED"
   Declarative hooks:
     [data-tabs] + [data-tab="name"] + [data-tab-panel="name"]   switch panels
     [data-toggle-password="#inputId"]                            show / hide password
   ========================================================================== */
(function () {
    "use strict";

    var DS = window.DocuSecure = window.DocuSecure || {};

    DS.setFieldError = function (input, message) {
        var field = input.closest(".field");
        if (!field) return;
        var error = field.querySelector(".field__error");
        if (!error) {
            error = document.createElement("div");
            error.className = "field__error";
            error.setAttribute("role", "alert");
            field.appendChild(error);
        }
        error.textContent = message;
        input.classList.add("is-invalid");
        input.setAttribute("aria-invalid", "true");
    };

    DS.clearFieldError = function (input) {
        var field = input.closest(".field");
        input.classList.remove("is-invalid");
        input.removeAttribute("aria-invalid");
        if (!field) return;
        var error = field.querySelector(".field__error");
        if (error) error.remove();
    };

    DS.clearFormErrors = function (form) {
        Array.prototype.forEach.call(form.querySelectorAll(".field__error"), function (el) { el.remove(); });
        Array.prototype.forEach.call(form.querySelectorAll(".is-invalid"), function (el) {
            el.classList.remove("is-invalid");
            el.removeAttribute("aria-invalid");
        });
    };

    DS.initialsOf = function (name) {
        var parts = String(name).replace(/^Dr\.\s+/i, "").trim().split(/\s+/);
        return (parts[0] ? parts[0].charAt(0) : "") + (parts[1] ? parts[1].charAt(0) : "");
    };

    DS.isEmail = function (value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    };

    /* Clear a field's error as soon as the person edits it */
    document.addEventListener("input", function (event) {
        var target = event.target;
        if (target.classList && target.classList.contains("is-invalid")) DS.clearFieldError(target);
    });
    document.addEventListener("change", function (event) {
        var target = event.target;
        if (target.classList && target.classList.contains("is-invalid")) DS.clearFieldError(target);
    });

    /* Tab panels */
    function initTabPanels() {
        Array.prototype.forEach.call(document.querySelectorAll("[data-tabs]"), function (container) {
            var tabs = Array.prototype.slice.call(container.querySelectorAll("[data-tab]"));
            var panels = Array.prototype.slice.call(document.querySelectorAll("[data-tab-panel]"));

            function activate(name) {
                tabs.forEach(function (tab) {
                    var on = tab.getAttribute("data-tab") === name;
                    tab.classList.toggle("is-active", on);
                    tab.setAttribute("aria-selected", String(on));
                });
                panels.forEach(function (panel) {
                    panel.hidden = panel.getAttribute("data-tab-panel") !== name;
                });
            }

            tabs.forEach(function (tab) {
                tab.addEventListener("click", function () { activate(tab.getAttribute("data-tab")); });
            });

            var fromHash = (window.location.hash || "").replace("#", "");
            var exists = tabs.some(function (tab) { return tab.getAttribute("data-tab") === fromHash; });
            var initial = exists ? fromHash : (tabs[0] && tabs[0].getAttribute("data-tab"));
            if (initial) activate(initial);
        });
    }

    /* Password visibility */
    document.addEventListener("click", function (event) {
        var button = event.target.closest("[data-toggle-password]");
        if (!button) return;
        var input = document.querySelector(button.getAttribute("data-toggle-password"));
        if (!input) return;
        var show = input.type === "password";
        input.type = show ? "text" : "password";
        button.setAttribute("aria-label", show ? "Hide password" : "Show password");
        button.setAttribute("aria-pressed", String(show));
        var use = button.querySelector("use");
        if (use) use.setAttribute("href", show ? "#i-eye-off" : "#i-eye");
    });

    document.addEventListener("DOMContentLoaded", initTabPanels);
})();
