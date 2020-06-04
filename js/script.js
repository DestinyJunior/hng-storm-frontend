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

    // insert page at the top of the table
    table.parentNode.insertBefore(pager, document.querySelector('#insert'));  
}

function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  var table = document.querySelector("#table");
  input = document.getElementById("input");
  filter = input.value.toUpperCase();
  if (filter) {
    var rows = table.rows;
    for (var x = 0; x < rows.length; x++) {
      rows[x].classList.remove("inactive");
    }
    const paginator = document.querySelector('.pager')
    if (!!paginator === true) {
      paginator.remove();
    }
  } else {
    genTables()
  }

  table = document.getElementById("table");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];

    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

window.addEventListener('load', function() {
    genTables();
});