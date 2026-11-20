/* ==========================================================================
   Login page – FRONTEND DEMONSTRATION ONLY.

   Nothing here authenticates anyone. Every rule below (lockout counter, MFA, status
   messages, "any 6 digits") only simulates what the Laravel backend must enforce:
     - login throttling / lockout  -> RateLimiter per account AND per IP
     - password check              -> Hash::check against a bcrypt/argon2 hash
     - MFA verification            -> TOTP checked on the server
     - session expiry              -> server-side session lifetime
   Security-conscious UI choices that DO carry over to the real system:
     - generic failure message (no "email not found" vs "wrong password" difference)
     - password field is cleared after a failure / step change
     - passwords are never logged, stored, or put in a URL
     - the ?reason= banner uses an allowlist (no reflected text) and no ?next= redirect exists
   ========================================================================== */
(function () {
    "use strict";

    var MAX_ATTEMPTS = 5;
    var ICONS = { info: "info", success: "check-circle", warning: "clock", danger: "lock" };
    var REASONS = {
        expired: ["warning", "Session expired", "For your security you were signed out after a period of inactivity. Please sign in again."],
        loggedout: ["success", "Signed out", "You have been signed out of DocuSecure."]
    };

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var alertBox = document.getElementById("auth-alert");
        var form = document.getElementById("login-form");
        var emailInput = document.getElementById("login-email");
        var passwordInput = document.getElementById("login-password");
        var submitBtn = document.getElementById("login-submit");
        var mfaForm = document.getElementById("mfa-form");
        var mfaInput = document.getElementById("mfa-code");
        var forgotForm = document.getElementById("forgot-form");
        var forgotInput = document.getElementById("forgot-email");
        var failedAttempts = 0; // in memory only on purpose: this is a demo, not a control
        var locked = false;
        var busy = false;

        /* ---- Alerts (built with textContent: never inject untrusted HTML) ---- */
        function hideAlert() {
            while (alertBox.firstChild) alertBox.removeChild(alertBox.firstChild);
        }

        function showAlert(type, title, message) {
            hideAlert();
            var box = document.createElement("div");
            box.className = "alert alert--" + type;
            box.setAttribute("role", type === "danger" || type === "warning" ? "alert" : "status");

            var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.setAttribute("class", "icon");
            svg.setAttribute("aria-hidden", "true");
            var use = document.createElementNS("http://www.w3.org/2000/svg", "use");
            use.setAttribute("href", "#i-" + ICONS[type]);
            svg.appendChild(use);

            var text = document.createElement("div");
            var strong = document.createElement("strong");
            strong.textContent = title;
            text.appendChild(strong);
            text.appendChild(document.createTextNode(message));

            box.appendChild(svg);
            box.appendChild(text);
            alertBox.appendChild(box);
        }

        /* ---- Password field helpers ---- */
        function clearPassword() {
            passwordInput.value = "";
            if (passwordInput.type !== "password") {
                passwordInput.type = "password";
                var toggle = document.querySelector('[data-toggle-password="#login-password"]');
                if (toggle) {
                    toggle.setAttribute("aria-label", "Show password");
                    toggle.setAttribute("aria-pressed", "false");
                    var use = toggle.querySelector("use");
                    if (use) use.setAttribute("href", "#i-eye");
                }
            }
        }

        function setBusy(state) {
            busy = state;
            submitBtn.disabled = state || locked;
            submitBtn.textContent = state ? "Signing in…" : "Sign in";
        }

        function lockAccount() {
            locked = true;
            emailInput.disabled = true;
            passwordInput.disabled = true;
            submitBtn.disabled = true;
            clearPassword();
            showAlert("danger", "Account temporarily locked", "Too many failed sign-in attempts. Try again in 15 minutes or contact your administrator.");
        }

        function unlockAccount() {
            locked = false;
            failedAttempts = 0;
            emailInput.disabled = false;
            passwordInput.disabled = false;
            submitBtn.disabled = false;
            hideAlert();
        }

        /* ---- Steps ---- */
        function showMfaStep() {
            form.hidden = true;
            mfaForm.hidden = false;
            document.getElementById("login-title").textContent = "Two-step verification";
            document.getElementById("login-subtitle").textContent = "Confirm it's you to finish signing in.";
            showAlert("info", "Verification required", "Demo: any 6-digit code is accepted because no server checks it.");
            mfaInput.value = "";
            mfaInput.focus();
        }

        function showCredentialsStep() {
            mfaForm.hidden = true;
            form.hidden = false;
            DS.clearFormErrors(mfaForm);
            document.getElementById("login-title").textContent = "Welcome back";
            document.getElementById("login-subtitle").textContent = "Sign in to your secure workspace.";
            hideAlert();
            emailInput.focus();
        }

        /* ---- Credentials ---- */
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            if (busy) return;
            DS.clearFormErrors(form);
            hideAlert();

            if (locked) {
                lockAccount();
                return;
            }

            var email = emailInput.value.trim();
            var valid = true;
            if (!email) {
                DS.setFieldError(emailInput, "Enter your email address.");
                valid = false;
            } else if (!DS.isEmail(email)) {
                DS.setFieldError(emailInput, "Enter a valid email address.");
                valid = false;
            }
            if (!passwordInput.value) {
                DS.setFieldError(passwordInput, "Enter your password.");
                valid = false;
            }
            if (!valid) {
                var firstInvalid = form.querySelector(".is-invalid");
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            setBusy(true);
            window.setTimeout(function () {
                clearPassword();
                setBusy(false);
                showMfaStep();
            }, 700);
        });

        /* ---- MFA ---- */
        mfaInput.addEventListener("input", function () {
            mfaInput.value = mfaInput.value.replace(/\D/g, "").slice(0, 6);
        });

        mfaForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(mfaForm);
            if (!/^\d{6}$/.test(mfaInput.value)) {
                DS.setFieldError(mfaInput, "Enter the 6-digit code from your authenticator app.");
                mfaInput.focus();
                return;
            }
            DS.toast("Signed in (demo: no real authentication). Redirecting…", "success");
            // Fixed, server-rendered destination. There is deliberately no ?next= parameter (open-redirect risk).
            window.setTimeout(function () { window.location.href = form.getAttribute("data-redirect"); }, 900);
        });

        document.getElementById("mfa-back").addEventListener("click", showCredentialsStep);

        /* ---- Forgot password: the same neutral answer whether or not the account exists ---- */
        forgotForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(forgotForm);
            var email = forgotInput.value.trim();
            if (!DS.isEmail(email)) {
                DS.setFieldError(forgotInput, "Enter a valid email address.");
                return;
            }
            forgotInput.value = "";
            DS.closeModal(document.getElementById("forgot-modal"));
            showAlert("info", "Check your email", "If an account exists for that address, reset instructions have been sent. The link expires shortly.");
        });

        /* ---- Demo scenarios ---- */
        Array.prototype.forEach.call(document.querySelectorAll("[data-demo]"), function (button) {
            button.addEventListener("click", function () {
                var scenario = button.getAttribute("data-demo");
                if (mfaForm.hidden === false) showCredentialsStep();

                if (scenario === "reset") {
                    unlockAccount();
                    DS.toast("Demo reset.", "success");
                } else if (scenario === "lock") {
                    failedAttempts = MAX_ATTEMPTS;
                    lockAccount();
                } else if (scenario === "fail") {
                    if (locked) { lockAccount(); return; }
                    failedAttempts += 1;
                    clearPassword();
                    if (failedAttempts >= MAX_ATTEMPTS) {
                        lockAccount();
                    } else {
                        showAlert("danger", "Sign-in failed", "We couldn't sign you in with those details. Check your email and password and try again.");
                    }
                } else if (scenario === "disabled") {
                    showAlert("warning", "Account not active", "This account is deactivated. Contact your administrator. (A real system should reveal this only after the correct password.)");
                } else if (scenario === "expired") {
                    var reason = REASONS.expired;
                    showAlert(reason[0], reason[1], reason[2]);
                }
            });
        });

        /* ---- ?reason= banner (allowlist lookup; unknown values are ignored) ---- */
        var reasonKey = new URLSearchParams(window.location.search).get("reason");
        if (reasonKey && Object.prototype.hasOwnProperty.call(REASONS, reasonKey)) {
            var known = REASONS[reasonKey];
            showAlert(known[0], known[1], known[2]);
        }
    });
})();
