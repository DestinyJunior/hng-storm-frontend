var perPage = 2;

function genTables() {
    var tables = document.querySelectorAll(".pagination");
    for (var i = 0; i < tables.length; i++) {
        perPage = parseInt(tables[i].dataset.pagecount);
        createFooters(tables[i]);
        createTableMeta(tables[i]);
        loadTable(tables[i]);
    }
}

// based on current page, only show the elements in that range
function loadTable(table) {
    var startIndex = 0;

    if (table.querySelector('th'))
        startIndex = 1;

	console.log(startIndex);

    var start = (parseInt(table.dataset.currentpage) * table.dataset.pagecount) + startIndex;
    var end = start + parseInt(table.dataset.pagecount);
    var rows = table.rows;

    for (var x = startIndex; x < rows.length; x++) {
        if (x < start || x >= end)
            rows[x].classList.add("inactive");
        else
            rows[x].classList.remove("inactive");
    }
}

function createTableMeta(table) {
    table.dataset.currentpage = "0";
}

function createFooters(table) {
    var hasHeader = false;
    if (table.querySelector('th'))
        hasHeader = true;

    var rows = table.rows.length;

    if (hasHeader)
        rows = rows - 1;

    var numPages = rows / perPage;
    var pager = document.createElement("div");

    // add an extra page, if we're 
    if (numPages % 1 > 0)
        numPages = Math.floor(numPages) + 1;

    pager.className = "pager";
    for (var i = 0; i < numPages ; i++) {
        var page = document.createElement("div");
        page.innerHTML = i + 1;
        page.className = "pager-item";
        page.dataset.index = i;

        if (i == 0)
            page.classList.add("selected");

        page.addEventListener('click', function() {
            var parent = this.parentNode;
            var items = parent.querySelectorAll(".pager-item");
            for (var x = 0; x < items.length; x++) {
                items[x].classList.remove("selected");
            }
            this.classList.add('selected');
            table.dataset.currentpage = this.dataset.index;
            loadTable(table);
        });
        pager.appendChild(page);
    }
    console.log(pager)
    // insert page at the top of the table
    table.parentNode.insertBefore(pager, document.querySelector('#insert'));
}

window.addEventListener('load', function() {
    genTables();
});