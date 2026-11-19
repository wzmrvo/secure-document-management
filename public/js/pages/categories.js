/* Categories page: add, edit and delete categories (frontend state only). */
(function () {
    "use strict";

    var TONES = { Public: "neutral", Internal: "info", Confidential: "gold", Restricted: "danger" };

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var root = document.querySelector("[data-table]");
        if (!root) return;

        var grid = root.querySelector("[data-table-body]");
        var template = document.getElementById("category-template");
        var modal = document.getElementById("category-modal");
        var modalTitle = document.getElementById("category-modal-title");
        var form = document.getElementById("category-form");
        var nameInput = document.getElementById("category-name");
        var descInput = document.getElementById("category-description");
        var classSelect = document.getElementById("category-classification");
        var deleteText = document.getElementById("delete-category-text");
        var activeCard = null; // null while adding

        function documentsUrl(name) {
            var base = document.querySelector(".js-count-link").getAttribute("href").split("?")[0];
            return base + "?category=" + encodeURIComponent(name);
        }

        function fillCard(card, data) {
            card.dataset.name = data.name;
            card.dataset.classification = data.classification;
            card.querySelector(".js-name").textContent = data.name;
            card.querySelector(".js-desc").textContent = data.description || "No description yet.";
            var badge = card.querySelector(".js-class");
            badge.textContent = data.classification;
            badge.className = "badge badge--" + TONES[data.classification] + " js-class";
            card.querySelector(".js-count-link").setAttribute("href", documentsUrl(data.name));
        }

        function nameTaken(name, ignoreCard) {
            return Array.prototype.some.call(grid.querySelectorAll("[data-row]"), function (card) {
                return card !== ignoreCard && card.dataset.name.toLowerCase() === name.toLowerCase();
            });
        }

        function openForm(card) {
            activeCard = card;
            DS.clearFormErrors(form);
            if (card) {
                modalTitle.textContent = "Edit category";
                nameInput.value = card.dataset.name;
                var desc = card.querySelector(".js-desc").textContent;
                descInput.value = desc === "No description yet." ? "" : desc;
                classSelect.value = card.dataset.classification;
            } else {
                modalTitle.textContent = "Add category";
                form.reset();
                classSelect.value = "Internal";
            }
            DS.openModal(modal);
        }

        document.getElementById("add-category-btn").addEventListener("click", function () { openForm(null); });

        grid.addEventListener("click", function (event) {
            var button = event.target.closest("[data-category-action]");
            if (!button) return;
            var card = button.closest("[data-row]");
            if (button.getAttribute("data-category-action") === "edit") {
                openForm(card);
            } else {
                activeCard = card;
                var count = parseInt(card.querySelector(".js-count").textContent, 10) || 0;
                deleteText.textContent = count > 0
                    ? '"' + card.dataset.name + '" contains ' + count + " document(s). They would become uncategorized. This is a frontend demo, so nothing is changed on a server."
                    : '"' + card.dataset.name + '" is empty and will be removed.';
                DS.openModal(document.getElementById("delete-category-modal"));
            }
        });

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(form);
            var name = nameInput.value.trim();
            if (name.length < 2) {
                DS.setFieldError(nameInput, "Enter a category name of at least 2 characters.");
                return;
            }
            if (nameTaken(name, activeCard)) {
                DS.setFieldError(nameInput, "A category with this name already exists.");
                return;
            }
            var data = { name: name, description: descInput.value.trim(), classification: classSelect.value };

            if (activeCard) {
                fillCard(activeCard, data);
                DS.toast("Category updated.", "success");
            } else {
                var card = template.content.firstElementChild.cloneNode(true);
                fillCard(card, data);
                card.querySelector(".js-count").textContent = "0";
                grid.insertBefore(card, grid.firstChild);
                DS.toast('Category "' + name + '" added.', "success");
            }
            DS.closeModal(modal);
            root.dataTable.refresh();
        });

        document.getElementById("confirm-delete-category").addEventListener("click", function () {
            if (!activeCard) return;
            var name = activeCard.dataset.name;
            activeCard.remove();
            activeCard = null;
            root.dataTable.refresh();
            DS.toast('Category "' + name + '" deleted.', "success");
        });
    });
})();
