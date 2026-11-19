/* Access Requests page: approve / reject with confirmation, tab counts update automatically. */
(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var root = document.querySelector("[data-table]");
        if (!root) return;

        var modal = document.getElementById("access-confirm-modal");
        var modalTitle = document.getElementById("access-confirm-modal-title");
        var modalText = document.getElementById("access-confirm-text");
        var confirmBtn = document.getElementById("access-confirm-btn");
        var pending = null; // { row, action }

        root.addEventListener("click", function (event) {
            var button = event.target.closest("[data-access-action]");
            if (!button) return;
            var row = button.closest("[data-row]");
            var action = button.getAttribute("data-access-action");
            pending = { row: row, action: action };

            var approve = action === "approve";
            modalTitle.textContent = (approve ? "Approve" : "Reject") + " access for " + row.dataset.user + "?";
            modalText.textContent = approve
                ? 'They will be able to open "' + row.dataset.document + '" with "' + row.dataset.permission + '" permission.'
                : 'The request for "' + row.dataset.document + '" will be marked as rejected and the requester notified.';
            confirmBtn.className = "btn " + (approve ? "btn--primary" : "btn--danger");
            DS.openModal(modal);
        });

        confirmBtn.addEventListener("click", function () {
            if (!pending) return;
            var row = pending.row;
            var approve = pending.action === "approve";
            var status = approve ? "approved" : "rejected";

            row.dataset.status = status;
            var actions = row.querySelector(".js-actions");
            actions.innerHTML = "";
            var badge = document.createElement("span");
            badge.className = "badge badge--" + (approve ? "success" : "danger");
            badge.textContent = approve ? "Approved" : "Rejected";
            var result = document.createElement("span");
            result.className = "request-card__result request-card__result--" + status;
            result.textContent = approve ? "Access granted" : "Request rejected";
            actions.appendChild(badge);
            actions.appendChild(result);

            DS.toast(row.dataset.user + " – request " + status + ".", approve ? "success" : "danger");
            pending = null;
            root.dataTable.refresh();
        });
    });
})();
