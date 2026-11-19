/* Settings page: profile + password validation, toggle feedback (frontend state only). */
(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;

        /* Profile */
        var profileForm = document.getElementById("profile-form");
        var nameInput = document.getElementById("profile-name");
        var emailInput = document.getElementById("profile-email");

        profileForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(profileForm);
            var valid = true;
            if (nameInput.value.trim().length < 2) {
                DS.setFieldError(nameInput, "Enter your full name.");
                valid = false;
            }
            if (!DS.isEmail(emailInput.value.trim())) {
                DS.setFieldError(emailInput, "Enter a valid email address.");
                valid = false;
            }
            if (!valid) return;
            DS.toast("Profile saved.", "success");
        });

        /* Password */
        var passwordForm = document.getElementById("password-form");
        var current = document.getElementById("current-password");
        var next = document.getElementById("new-password");
        var confirm = document.getElementById("confirm-password");

        passwordForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(passwordForm);
            var valid = true;

            if (!current.value) {
                DS.setFieldError(current, "Enter your current password.");
                valid = false;
            }
            if (next.value.length < 8) {
                DS.setFieldError(next, "Use at least 8 characters.");
                valid = false;
            } else if (!/[A-Za-z]/.test(next.value) || !/[0-9]/.test(next.value)) {
                DS.setFieldError(next, "Include both letters and numbers.");
                valid = false;
            } else if (next.value === current.value) {
                DS.setFieldError(next, "The new password must be different from the current one.");
                valid = false;
            }
            if (confirm.value !== next.value) {
                DS.setFieldError(confirm, "Passwords do not match.");
                valid = false;
            }
            if (!valid) return;

            passwordForm.reset();
            DS.toast("Password updated (demo: nothing is stored).", "success");
        });

        /* Toggles and selects give feedback */
        document.addEventListener("change", function (event) {
            var control = event.target.closest("[data-setting-label]");
            if (!control) return;
            var label = control.getAttribute("data-setting-label");
            if (control.type === "checkbox") {
                DS.toast(label + (control.checked ? " turned on." : " turned off."), "success");
            } else {
                DS.toast(label + " set to " + control.value + ".", "success");
            }
        });
    });
})();
