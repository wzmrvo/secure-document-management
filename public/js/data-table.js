/* ==========================================================================
   DocuSecure – generic list controller: search, filters, tabs, sorting, pagination.
   Works on tables AND card lists. Markup contract (all optional except body):

   <div data-table data-page-size="7" data-today="2026-11-19">
       <input data-table-search>                          text search over each row's text
       <select data-table-filter="category">              matches row[data-category]
       <select data-table-filter="range">                 option value = number of days (uses row[data-date])
       <div data-table-tabs="classification">             buttons [data-value] match row[data-classification]
           <button data-value="all" class="is-active">…<span data-count></span></button>
       <th data-sort="date" data-sort-type="text|number"> sorts by row[data-date]
       <tbody data-table-body> <tr data-row data-category="…"> …
       <div data-table-empty hidden>   <span data-table-count data-noun="documents">   <div data-pagination>

   Also reads ?q=… and ?<filterName>=… from the URL (used by the header search).
   After changing rows in the DOM (add / edit / remove) call element.dataTable.refresh().
   ========================================================================== */
(function () {
    "use strict";

    function toArray(list) { return Array.prototype.slice.call(list); }

    function DataTable(root) {
        var body = root.querySelector("[data-table-body]");
        if (!body) return;

        var searchInput = root.querySelector("[data-table-search]");
        var filters = toArray(root.querySelectorAll("[data-table-filter]"));
        var tabsEl = root.querySelector("[data-table-tabs]");
        var tabButtons = tabsEl ? toArray(tabsEl.querySelectorAll("[data-value]")) : [];
        var tabAttr = tabsEl ? tabsEl.getAttribute("data-table-tabs") : null;
        var sortHeaders = toArray(root.querySelectorAll("[data-sort]"));
        var emptyEl = root.querySelector("[data-table-empty]");
        var countEl = root.querySelector("[data-table-count]");
        var pagerEl = root.querySelector("[data-pagination]");
        var pageSize = parseInt(root.getAttribute("data-page-size") || "0", 10);
        var state = { query: "", tab: "all", page: 1, sortKey: null, sortDir: 1, sortType: "text" };
        var lastFiltered = [];

        var activeTab = tabButtons.filter(function (b) { return b.classList.contains("is-active"); })[0];
        if (activeTab) state.tab = activeTab.getAttribute("data-value");

        /* Initial state from the URL */
        var params = new URLSearchParams(window.location.search);
        if (searchInput && params.get("q")) {
            searchInput.value = params.get("q");
            state.query = searchInput.value.trim().toLowerCase();
        }
        filters.forEach(function (select) {
            var wanted = params.get(select.getAttribute("data-table-filter"));
            if (!wanted) return;
            toArray(select.options).forEach(function (option) {
                if (option.value === wanted) select.value = wanted;
            });
        });

        function rows() { return toArray(body.querySelectorAll("[data-row]")); }

        function withinDays(dateString, days) {
            var todayString = root.getAttribute("data-today") || new Date().toISOString().slice(0, 10);
            var today = new Date(todayString + "T00:00:00");
            var date = new Date(String(dateString).slice(0, 10) + "T00:00:00");
            return (today - date) / 86400000 <= days;
        }

        function rowMatches(row) {
            if (state.query && row.textContent.toLowerCase().indexOf(state.query) === -1) return false;
            for (var i = 0; i < filters.length; i++) {
                var attr = filters[i].getAttribute("data-table-filter");
                var value = filters[i].value;
                if (!value || value === "all") continue;
                if (attr === "range") {
                    if (!withinDays(row.dataset.date, parseInt(value, 10))) return false;
                } else if (row.dataset[attr] !== value) {
                    return false;
                }
            }
            if (tabAttr && state.tab !== "all" && row.dataset[tabAttr] !== state.tab) return false;
            return true;
        }

        function sortRows(list) {
            if (!state.sortKey) return list;
            return list
                .map(function (row, index) { return { row: row, index: index }; })
                .sort(function (a, b) {
                    var x = a.row.dataset[state.sortKey] || "";
                    var y = b.row.dataset[state.sortKey] || "";
                    var result = state.sortType === "number"
                        ? parseFloat(x) - parseFloat(y)
                        : x.localeCompare(y, undefined, { numeric: true, sensitivity: "base" });
                    return result ? result * state.sortDir : a.index - b.index;
                })
                .map(function (item) { return item.row; });
        }

        function addPagerButton(label, page, disabled, active) {
            var button = document.createElement("button");
            button.type = "button";
            button.className = "pagination__btn" + (active ? " is-active" : "");
            button.textContent = label;
            button.disabled = !!disabled;
            if (active) button.setAttribute("aria-current", "page");
            button.addEventListener("click", function () { state.page = page; render(); });
            pagerEl.appendChild(button);
        }

        function renderPager(pages) {
            if (!pagerEl) return;
            pagerEl.innerHTML = "";
            if (!pageSize || pages <= 1) { pagerEl.hidden = true; return; }
            pagerEl.hidden = false;
            addPagerButton("Previous", state.page - 1, state.page === 1, false);
            for (var p = 1; p <= pages; p++) addPagerButton(String(p), p, false, p === state.page);
            addPagerButton("Next", state.page + 1, state.page === pages, false);
        }

        function updateTabCounts(all) {
            tabButtons.forEach(function (button) {
                var counter = button.querySelector("[data-count]");
                if (!counter) return;
                var value = button.getAttribute("data-value");
                counter.textContent = value === "all"
                    ? all.length
                    : all.filter(function (row) { return row.dataset[tabAttr] === value; }).length;
            });
        }

        function updateSortHeaders() {
            sortHeaders.forEach(function (header) {
                var active = header.getAttribute("data-sort") === state.sortKey;
                header.setAttribute("aria-sort", active ? (state.sortDir === 1 ? "ascending" : "descending") : "none");
            });
        }

        function render() {
            var all = rows();
            var filtered = sortRows(all.filter(rowMatches));
            lastFiltered = filtered;

            var total = filtered.length;
            var pages = pageSize ? Math.max(1, Math.ceil(total / pageSize)) : 1;
            if (state.page > pages) state.page = pages;
            var start = pageSize ? (state.page - 1) * pageSize : 0;
            var end = pageSize ? start + pageSize : total;
            var visible = filtered.slice(start, end);

            var rest = all.filter(function (row) { return filtered.indexOf(row) === -1; });
            filtered.concat(rest).forEach(function (row) { body.appendChild(row); });
            all.forEach(function (row) { row.hidden = visible.indexOf(row) === -1; });

            if (emptyEl) emptyEl.hidden = total !== 0;
            if (countEl) {
                var noun = countEl.getAttribute("data-noun") || "items";
                countEl.textContent = total
                    ? "Showing " + (start + 1) + "–" + Math.min(end, total) + " of " + total + " " + noun
                    : "No " + noun + " to show";
            }
            renderPager(pages);
            updateTabCounts(all);
            updateSortHeaders();
        }

        if (searchInput) {
            searchInput.addEventListener("input", function () {
                state.query = searchInput.value.trim().toLowerCase();
                state.page = 1;
                render();
            });
        }
        filters.forEach(function (select) {
            select.addEventListener("change", function () { state.page = 1; render(); });
        });
        tabButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                state.tab = button.getAttribute("data-value");
                state.page = 1;
                tabButtons.forEach(function (other) {
                    var on = other === button;
                    other.classList.toggle("is-active", on);
                    other.setAttribute("aria-selected", String(on));
                });
                render();
            });
        });
        sortHeaders.forEach(function (header) {
            header.classList.add("th-sort");
            header.tabIndex = 0;
            function toggle() {
                var key = header.getAttribute("data-sort");
                if (state.sortKey === key) { state.sortDir *= -1; } else { state.sortKey = key; state.sortDir = 1; }
                state.sortType = header.getAttribute("data-sort-type") || "text";
                render();
            }
            header.addEventListener("click", toggle);
            header.addEventListener("keydown", function (event) {
                if (event.key === "Enter" || event.key === " ") { event.preventDefault(); toggle(); }
            });
        });

        root.dataTable = {
            refresh: render,
            getFilteredRows: function () { return lastFiltered.slice(); }
        };
        render();
    }

    document.addEventListener("DOMContentLoaded", function () {
        toArray(document.querySelectorAll("[data-table]")).forEach(DataTable);
    });
})();
