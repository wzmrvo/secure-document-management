/* Upload Document page: drag & drop, file preview, validation, classification note. */
(function () {
    "use strict";

    var ALLOWED = ["pdf", "docx", "xlsx", "pptx"];
    var MAX_BYTES = 25 * 1024 * 1024;
    var NOTES = {
        Public: ["Public by default", "Anyone with a DocuSecure account can view this file. Access is still logged."],
        Internal: ["Internal by default", "Visible to staff and authorized users. Access is logged."],
        Confidential: ["Confidential by default", "Access is logged. Only permitted users can preview or download this file."],
        Restricted: ["Restricted by default", "Only administrators and approved users can open this file. Every access is logged."]
    };

    function formatSize(bytes) {
        if (bytes < 1024 * 1024) return Math.max(1, Math.round(bytes / 1024)) + " KB";
        return (bytes / (1024 * 1024)).toFixed(1) + " MB";
    }

    document.addEventListener("DOMContentLoaded", function () {
        var DS = window.DocuSecure;
        var form = document.getElementById("upload-form");
        var titleInput = document.getElementById("doc-title");
        var descInput = document.getElementById("doc-description");
        var descCount = document.getElementById("description-count");
        var categorySelect = document.getElementById("doc-category");
        var dropzone = document.getElementById("dropzone");
        var fileInput = document.getElementById("file-input");
        var fileRow = document.getElementById("file-row");
        var fileName = document.getElementById("file-name");
        var fileSize = document.getElementById("file-size");
        var fileError = document.getElementById("file-error");
        var selectedFile = null;

        function showFileError(message) {
            fileError.textContent = message;
            fileError.hidden = !message;
        }

        function clearFile() {
            selectedFile = null;
            fileInput.value = "";
            fileRow.hidden = true;
        }

        function setFile(file) {
            showFileError("");
            var extension = file.name.split(".").pop().toLowerCase();
            if (ALLOWED.indexOf(extension) === -1) {
                clearFile();
                showFileError("Unsupported file type. Use PDF, DOCX, XLSX, or PPTX.");
                return;
            }
            if (file.size > MAX_BYTES) {
                clearFile();
                showFileError("File is too large. The maximum size is 25 MB.");
                return;
            }
            selectedFile = file;
            fileName.textContent = file.name;
            fileSize.textContent = formatSize(file.size);
            fileRow.hidden = false;
            if (!titleInput.value.trim()) {
                titleInput.value = file.name.replace(/\.[^.]+$/, "");
                DS.clearFieldError(titleInput);
            }
        }

        /* Drag & drop and browse */
        dropzone.addEventListener("click", function () { fileInput.click(); });
        dropzone.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") { event.preventDefault(); fileInput.click(); }
        });
        ["dragenter", "dragover"].forEach(function (name) {
            dropzone.addEventListener(name, function (event) {
                event.preventDefault();
                dropzone.classList.add("is-dragover");
            });
        });
        ["dragleave", "drop"].forEach(function (name) {
            dropzone.addEventListener(name, function (event) {
                event.preventDefault();
                dropzone.classList.remove("is-dragover");
            });
        });
        dropzone.addEventListener("drop", function (event) {
            var files = event.dataTransfer && event.dataTransfer.files;
            if (files && files[0]) setFile(files[0]);
        });
        fileInput.addEventListener("change", function () {
            if (fileInput.files && fileInput.files[0]) setFile(fileInput.files[0]);
        });
        document.getElementById("file-remove").addEventListener("click", clearFile);

        /* Description counter */
        descInput.addEventListener("input", function () { descCount.textContent = descInput.value.length; });

        /* Classification note */
        Array.prototype.forEach.call(form.querySelectorAll('input[name="classification"]'), function (radio) {
            radio.addEventListener("change", function () {
                var note = NOTES[radio.value];
                document.getElementById("note-title").textContent = note[0];
                document.getElementById("note-text").textContent = note[1];
            });
        });

        /* Submit */
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            DS.clearFormErrors(form);
            showFileError("");
            var valid = true;

            if (titleInput.value.trim().length < 3) {
                DS.setFieldError(titleInput, "Enter a document title of at least 3 characters.");
                valid = false;
            }
            if (!categorySelect.value) {
                DS.setFieldError(categorySelect, "Select a category.");
                valid = false;
            }
            if (!selectedFile) {
                showFileError("Choose a file to upload.");
                valid = false;
            }
            if (!valid) {
                var firstInvalid = form.querySelector(".is-invalid");
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            DS.toast('"' + titleInput.value.trim() + '" submitted (demo: the file is not stored).', "success");
            window.setTimeout(function () { window.location.href = form.getAttribute("data-redirect"); }, 1400);
        });
    });
})();
