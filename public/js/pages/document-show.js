/* Document Details page: edit modal, download (demo), request-access modal. */
(function () {
    "use strict";

    var TONES = { Public: "neutral", Internal: "info", Confidential: "gold", Restricted: "danger" };

    function bindAll(name, value) {
        Array.prototype.forEach.call(document.querySelectorAll('[data-bind="' + name + '"]'), function (el) {
            el.textContent = value;
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var rules = window.DocuSecureAccessRules || {};

        /* Download */
        document.getElementById("download-btn").addEventListener("click", function () {
            DS.toast("Download started (demo: no file is served yet).", "success");
        });

        /* Edit details */
        var editForm = document.getElementById("edit-details-form");
        var editModal = document.getElementById("edit-details-modal");
        var titleInput = document.getElementById("edit-title");
        var categorySelect = document.getElementById("edit-category");
        var classSelect = document.getElementById("edit-classification");
        var originalFile = document.querySelector('[data-bind="file"]').textContent;
        var extension = originalFile.indexOf(".") > -1 ? originalFile.slice(originalFile.lastIndexOf(".")) : "";

        editForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(editForm);
            var title = titleInput.value.trim();
            if (title.length < 3) {
                DS.setFieldError(titleInput, "Enter a title of at least 3 characters.");
                return;
            }
            bindAll("title", title);
            bindAll("file", title + extension);
            bindAll("category", categorySelect.value);
            bindAll("classification", classSelect.value);

            var badge = document.getElementById("class-badge");
            badge.textContent = classSelect.value;
            badge.className = "badge badge--" + TONES[classSelect.value];

            var list = document.getElementById("access-list");
            list.innerHTML = "";
            (rules[classSelect.value] || []).forEach(function (rule) {
                var item = document.createElement("li");
                item.appendChild(document.createTextNode(rule[0] + " · "));
                var span = document.createElement("span");
                span.textContent = rule[1];
                item.appendChild(span);
                list.appendChild(item);
            });

            document.title = title + " · DocuSecure";
            DS.closeModal(editModal);
            DS.toast("Document details updated.", "success");
        });

        /* Request access (unauthorized-user view) */
        var requestForm = document.getElementById("request-access-form");
        var reasonInput = document.getElementById("request-reason");
        requestForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(requestForm);
            if (reasonInput.value.trim().length < 10) {
                DS.setFieldError(reasonInput, "Please explain your reason in at least 10 characters.");
                return;
            }
            reasonInput.value = "";
            DS.closeModal(document.getElementById("request-access-modal"));
            DS.toast("Access request sent. An administrator will review it.", "success");
        });
    });
})();
