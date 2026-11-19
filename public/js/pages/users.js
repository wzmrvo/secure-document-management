/* User Management page: role cards as filters, add / edit users, activate / deactivate (frontend state only). */
(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var root = document.querySelector("[data-table]");
        if (!root) return;

        var body = root.querySelector("[data-table-body]");
        var template = document.getElementById("user-row-template");
        var modal = document.getElementById("user-modal");
        var modalTitle = document.getElementById("user-modal-title");
        var form = document.getElementById("user-form");
        var nameInput = document.getElementById("user-name");
        var emailInput = document.getElementById("user-email");
        var roleSelect = document.getElementById("user-role");
        var statusSelect = document.getElementById("user-status");
        var roleFilter = document.getElementById("role-filter");
        var roleCards = Array.prototype.slice.call(document.querySelectorAll("[data-role-card]"));
        var activeRow = null; // null while adding

        function rows() { return Array.prototype.slice.call(body.querySelectorAll("[data-row]")); }

        function updateRoleCounts() {
            Array.prototype.forEach.call(document.querySelectorAll("[data-role-count]"), function (el) {
                var role = el.getAttribute("data-role-count");
                el.textContent = rows().filter(function (row) { return row.dataset.role === role; }).length;
            });
        }

        function syncRoleCards() {
            roleCards.forEach(function (card) {
                var on = card.getAttribute("data-role-card") === roleFilter.value;
                card.classList.toggle("is-active", on);
                card.setAttribute("aria-pressed", String(on));
            });
        }

        function applyStatus(row, status) {
            row.dataset.status = status;
            var badge = row.querySelector(".js-status");
            badge.textContent = status;
            badge.className = "badge badge--" + (status === "Active" ? "success" : "neutral") + " js-status";
            var toggle = row.querySelector(".js-toggle");
            toggle.textContent = status === "Active" ? "Deactivate" : "Activate";
            toggle.className = "text-link-btn " + (status === "Active" ? "text-link-btn--danger " : "") + "js-toggle";
        }

        function applyUser(row, data) {
            row.dataset.name = data.name;
            row.dataset.email = data.email;
            row.dataset.role = data.role;
            row.querySelector(".js-name").textContent = data.name;
            row.querySelector(".js-email").textContent = data.email;
            row.querySelector(".js-initials").textContent = DS.initialsOf(data.name).toUpperCase();
            row.querySelector(".js-role").textContent = data.role;
            applyStatus(row, data.status);
        }

        function emailTaken(email, ignoreRow) {
            return rows().some(function (row) {
                return row !== ignoreRow && row.dataset.email.toLowerCase() === email.toLowerCase();
            });
        }

        function openForm(row) {
            activeRow = row;
            DS.clearFormErrors(form);
            if (row) {
                modalTitle.textContent = "Edit user";
                nameInput.value = row.dataset.name;
                emailInput.value = row.dataset.email;
                roleSelect.value = row.dataset.role;
                statusSelect.value = row.dataset.status;
            } else {
                modalTitle.textContent = "Add user";
                form.reset();
            }
            DS.openModal(modal);
        }

        /* Role cards filter the table */
        roleCards.forEach(function (card) {
            card.addEventListener("click", function () {
                var role = card.getAttribute("data-role-card");
                roleFilter.value = roleFilter.value === role ? "all" : role;
                roleFilter.dispatchEvent(new Event("change"));
            });
        });
        roleFilter.addEventListener("change", syncRoleCards);
        syncRoleCards();

        document.getElementById("add-user-btn").addEventListener("click", function () { openForm(null); });

        body.addEventListener("click", function (event) {
            var button = event.target.closest("[data-user-action]");
            if (!button) return;
            var row = button.closest("[data-row]");
            if (button.getAttribute("data-user-action") === "edit") {
                openForm(row);
            } else {
                var next = row.dataset.status === "Active" ? "Inactive" : "Active";
                applyStatus(row, next);
                root.dataTable.refresh();
                DS.toast(row.dataset.name + " is now " + next.toLowerCase() + ".", "success");
            }
        });

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(form);
            var name = nameInput.value.trim();
            var email = emailInput.value.trim();
            var valid = true;

            if (name.length < 2) {
                DS.setFieldError(nameInput, "Enter the user's full name.");
                valid = false;
            }
            if (!DS.isEmail(email)) {
                DS.setFieldError(emailInput, "Enter a valid email address.");
                valid = false;
            } else if (emailTaken(email, activeRow)) {
                DS.setFieldError(emailInput, "Another user already has this email address.");
                valid = false;
            }
            if (!valid) return;

            var data = { name: name, email: email, role: roleSelect.value, status: statusSelect.value };
            if (activeRow) {
                applyUser(activeRow, data);
                DS.toast("User updated.", "success");
            } else {
                var row = template.content.firstElementChild.cloneNode(true);
                var today = new Date();
                row.dataset.date = today.toISOString().slice(0, 10);
                row.querySelector(".js-date").textContent = today.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
                applyUser(row, data);
                body.insertBefore(row, body.firstChild);
                DS.toast(name + " added.", "success");
            }
            DS.closeModal(modal);
            updateRoleCounts();
            root.dataTable.refresh();
        });
    });
})();
