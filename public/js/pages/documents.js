/* Documents page: edit, download (demo) and delete actions on table rows. */
(function () {
    "use strict";

    var TONES = { Public: "neutral", Internal: "info", Confidential: "gold", Restricted: "danger" };

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var root = document.querySelector("[data-table]");
        if (!root) return;

        var editModal = document.getElementById("edit-doc-modal");
        var editForm = document.getElementById("edit-doc-form");
        var titleInput = document.getElementById("edit-doc-title");
        var categorySelect = document.getElementById("edit-doc-category");
        var classSelect = document.getElementById("edit-doc-classification");
        var deleteName = document.getElementById("delete-doc-name");
        var confirmDelete = document.getElementById("confirm-delete-doc");
        var activeRow = null;

        root.addEventListener("click", function (event) {
            var button = event.target.closest("[data-doc-action]");
            if (!button) return;
            var row = button.closest("[data-row]");
            var action = button.getAttribute("data-doc-action");
            activeRow = row;

            if (action === "download") {
                DS.toast('Downloading "' + row.dataset.title + '" (demo: no file is served yet).', "success");
            } else if (action === "edit") {
                DS.clearFormErrors(editForm);
                titleInput.value = row.dataset.title;
                categorySelect.value = row.dataset.category;
                classSelect.value = row.dataset.classification;
                DS.openModal(editModal);
            } else if (action === "delete") {
                deleteName.textContent = row.dataset.title;
                DS.openModal(document.getElementById("delete-doc-modal"));
            }
        });

        editForm.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(editForm);
            var title = titleInput.value.trim();
            if (title.length < 3) {
                DS.setFieldError(titleInput, "Enter a file name of at least 3 characters.");
                return;
            }
            var row = activeRow;
            row.dataset.title = title;
            row.dataset.category = categorySelect.value;
            row.dataset.classification = classSelect.value;
            row.querySelector(".js-title").textContent = title;
            row.querySelector(".js-category").textContent = categorySelect.value;
            var badge = row.querySelector(".js-class");
            badge.textContent = classSelect.value;
            badge.className = "badge badge--" + TONES[classSelect.value] + " js-class";
            DS.closeModal(editModal);
            root.dataTable.refresh();
            DS.toast("Document updated.", "success");
        });

        confirmDelete.addEventListener("click", function () {
            if (!activeRow) return;
            var name = activeRow.dataset.title;
            activeRow.remove();
            activeRow = null;
            root.dataTable.refresh();
            DS.toast('"' + name + '" deleted.', "success");
        });
    });
})();
