document.addEventListener('DOMContentLoaded', function () {
    // event listener for refresh-btn button click
    document.getElementById("refresh-btn").addEventListener("click", function () {
        console.log("Refresh button clicked!");
        fetchTablesData();
    });

    // event listener for table-search-input input, look for table name in tables-list div
    document.getElementById("table-search-input").addEventListener("input", function () {
        console.log("Table search input changed!");
        let input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("table-search-input");
        filter = input.value.toUpperCase();
        ul = document.getElementById("db-tables-list");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByClassName("dbtbl-list-btn")[0];
            txtValue = a.id; // a.textContent || a.innerText;
            // get string between "focus-table-" and "-btn" which is the table name	
            txtValue = txtValue.substring(12, txtValue.length - 4);

            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                // li[i].style.display = "grid";
                // replace class d-none with d-grid
                li[i].classList.remove("d-none");
                li[i].classList.add("d-grid");
            } else {
                // li[i].style.display = "none";
                // replace class d-grid with d-none
                li[i].classList.remove("d-grid");
                li[i].classList.add("d-none");
            }
        }
    });

    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const elements = [];
    let zoom = 1;

    function drawLine(x1, y1, x2, y2, relationshipType) {
        // console.log("Drawing line...");
        ctx.beginPath();
        ctx.moveTo(x1 + 100, y1 + 50);
        ctx.lineTo(x2 + 100, y2 + 50);

        switch (relationshipType) {
            case 'one-to-many':
                ctx.strokeStyle = 'green';
                break;
            case 'many-to-one':
                ctx.strokeStyle = 'blue';
                break;
            case 'many-to-many':
                ctx.strokeStyle = 'orange';
                break;
            default:
                ctx.strokeStyle = 'red';
        }

        ctx.lineWidth = 5; // Set line thickness to 5px

        ctx.stroke();

        // Display relationship type
        ctx.fillText(relationshipType, (x1 + x2) / 2, (y1 + y2) / 2);
    }

    let throttleTimeout;

    function drawRelationshipLines() {
        // Use a throttle to prevent the function from being called too often // deprecate 
        if (throttleTimeout) {
            // If drawRelationshipLines is already scheduled, don't schedule it again
            return;
        }

        throttleTimeout = setTimeout(() => {
            console.log("Drawing relationship lines...");

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < elements.length; i++) {
                const { x: x1, y: y1 } = elements[i].getBoundingClientRect();
                const data1 = elements[i].data;

                data1.relatedTables.forEach(relatedTable => {
                    const relatedElement = elements.find(el => el.data.tableName === relatedTable.name);
                    if (relatedElement) {
                        const { x: x2, y: y2 } = relatedElement.getBoundingClientRect();
                        drawLine(x1, y1, x2, y2, relatedTable.type);
                    }
                });
            }

            // Clear the throttle timeout
            throttleTimeout = null;
        }, 100);  // Adjust the throttle time as needed
    }

    // function to position / place tables with more than one relationship on the left side of the canvas and tables with only one relationship on the right side of the canvas
    function verticallyAlignTables() {
        // console.log("Vertically aligning tables..."); // debug

        const midpoint = canvas.width / 2;
        let counterMoreThanOne = 0;
        let counterOne = 0;

        elements.forEach(element => {
            const numRelationships = element.data.relatedTables.length;
            const elementWidth = element.getBoundingClientRect().width;

            if (numRelationships > 1) {
                element.style.top = `${counterMoreThanOne * 300}px`;
                element.style.left = `${Math.max(0, (midpoint - elementWidth) - 200)}px`;
                counterMoreThanOne++;
            } else if (numRelationships === 1) {
                element.style.top = `${counterOne * 300}px`;
                element.style.left = `${Math.min(midpoint, canvas.width - elementWidth)}px`;
                counterOne++;
            }
        });

        // set the canvas size based on window size
        updateCanvasSize();
        // redraw relationship lines
        drawRelationshipLines();
    }

    function updateCanvasSize() {
        // console.log("Updating canvas size..."); // debug

        let maxRight = 0;
        let maxBottom = 0;
        let totalHeight = 0;

        elements.forEach(element => {
            const rect = element.getBoundingClientRect();
            totalHeight += rect.height;
            // console.log("Element height: " + rect.height);
            maxRight = Math.max(maxRight, rect.right);
            maxBottom = Math.max(maxBottom, rect.bottom);
        });

        maxRight = window.innerWidth;
        maxRight *= 2;
        canvas.width = maxRight; // totalHeight; // Math.max(document.innerWidth, maxRight);
        // canvas.height = maxBottom; // Math.max(document.innerHeight, maxBottom);
        totalHeight *= 1.2;
        canvas.height = totalHeight;
        // redraw relationship lines
        drawRelationshipLines();
        // console.log("Canvas width: " + canvas.width, "Canvas height: " + canvas.height); // debug
    }

    function init(tableData) {
        // console.log("Initializing...");
        showSnackbar("Initializing...");
        // initialize the canvas using the window size
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        // Remove all draggable elements from the DOM
        document.querySelectorAll('.draggable').forEach(el => el.remove());

        // clear the canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        tableData.forEach(data => {
            const element = createDraggableElement(data.tableName, data.columns, data.relatedTables, data.primaryKey);

            // update web pages html title attribute to display the db name
            document.title = data.dbName + " - Relational Viewer";
            document.getElementById("page-header-title").innerHTML = "Database: [ " + data.dbName + " ] - Relational Viewer (Version 1.0.0)";

            elements.push(element);
        });

        // set the canvas size based on window size
        // updateCanvasSize();

        document.addEventListener('resize', () => {
            // update the canvas size on page resize
            updateCanvasSize();
            // drawRelationshipLines();
        });

        document.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                // prevent the default zoom action
                // e.preventDefault();

                zoom += e.deltaY * -0.01;
                zoom = Math.min(Math.max(0.1, zoom), 3);

                console.log("Zooming... ( " + zoom + " )");

                // update the canvas size on page resize / zoom
                updateCanvasSize();
                // drawRelationshipLines();
            }
        });
        // , { passive: false }

        verticallyAlignTables();
        // drawRelationshipLines();
    }

    let collapseDetailsContainers = null; // collapse-details-${column.name}

    function createDraggableElement(tableName, columns, relatedTables, primaryKey) {
        console.log("Creating draggable element...");
        console.log("Table name: " + tableName);

        const element = document.createElement('div');
        element.className = 'draggable shadow rounded-4 bg-primary';
        element.innerHTML = `
          <h5 class="text-white"><span class="material-icons material-icons-round align-middle"> face </span> ${tableName}</h5>
          <div class="collapse multi-collapse show" id="collapse-details-${tableName}">
            <ol class="list-group list-group-numbered">
                ${columns.map(column => `<li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">${column.name}</div>
                            ${column.type}
                        </div>
                        ${column.isPrimaryKey ? ' <span class="badge bg-primary rounded-pill">PK</span>' : ''}
                    </li>`).join('')}
            </ol>
          </div>
        `;

        collapseDetailsContainers += `collapse-details-${tableName} `;

        // update button id="toggle-table-details-btn" aria-controls attribute 
        document.getElementById("toggle-table-details-btn").setAttribute("aria-controls", collapseDetailsContainers);

        // Attach additional data to the element
        element.data = { tableName, relatedTables, primaryKey };

        // Initial positioning with some randomness
        const x = Math.random() * (canvas.width - 200);
        const y = Math.random() * (canvas.width - 100);
        element.style.left = `${x}px`;
        element.style.top = `${y}px`;

        let offsetX, offsetY, isDragging = false;

        element.addEventListener('mousedown', (e) => {
            isDragging = true;
            element.isDragging = true; // Set isDragging property
            offsetX = e.clientX - element.getBoundingClientRect().left;
            offsetY = e.clientY - element.getBoundingClientRect().top;
            element.style.cursor = 'grabbing';
        });

        // Use window instead of document
        document.addEventListener('mousemove', (e) => {
            if (isDragging) {
                element.style.left = `${e.clientX - offsetX}px`;
                element.style.top = `${e.clientY - offsetY}px`;
                updateCanvasSize();
                // drawRelationshipLines();
            }
        });

        // Use window instead of document
        document.addEventListener('mouseup', () => {
            isDragging = false;
            element.isDragging = false; // Reset isDragging property
            element.style.cursor = 'grab';
            updateCanvasSize();
            // drawRelationshipLines();
        });

        document.body.appendChild(element);

        return element;
    }

    // copilot
    function addTable() {

        const tableName = prompt('Enter table name:');
        if (!tableName) return;

        const columnNames = [];
        let columnName;
        while (columnName = prompt('Enter column name (leave blank to finish):')) {
            columnNames.push(columnName);
        }

        const columns = columnNames.map(name => ({
            name,
            type: 'VARCHAR(255)',
            isPrimaryKey: false
        }));

        const element = createDraggableElement(tableName, columns);
        elements.push(element);
        // redraw relationship lines
        drawRelationshipLines();
    }

    function focusOnTable(tableName) {
        console.log("Focusing on table: " + tableName);
        alert("Focusing on table: " + tableName);

        // get db tables data from local storage
        let tablesData = JSON.parse(localStorage.getItem("tablesData"));
        // console.log("focus: Tables Data: ", tablesData);

        // Extract table name and columns and related tables from localstorage tableData and focus on it
        let focusTableData = tablesData.find(table => table.tableName === tableName);

        // convert tableData to be passed to init() function
        focusTableData = [
            {
                dbName: focusTableData.dbName,
                tableName: focusTableData.tableName,
                columns: focusTableData.columns,
                relatedTables: focusTableData.relatedTables,
                primaryKey: focusTableData.primaryKey
            }
        ];

        console.log("focus: Focused Table Data: ", focusTableData);

        // clear the canvas
        // ctx.clearRect(0, 0, canvas.width, canvas.height);
        // Call the init function with the tableData
        init(focusTableData);

        /* if (focusTableData) {
            if (tableData && tableData.data) {
                const { tableName, relatedTables, primaryKey } = tableData.data;
                console.log("tableData.data: ", tableData.data);
                const columns = tableData.data.columns;

                // Focus on the table and its related tables
                console.log("focus: Table Name: ", tableName);
                console.log("focus: Columns: ", columns);
                console.log("focus: Related Tables: ", relatedTables);
                console.log("focus: Primary Key: ", primaryKey);
            } else {
                console.log("focus: Table data is undefined or missing.");
            }

            // clear the canvas
            // ctx.clearRect(0, 0, canvas.width, canvas.height);

            // 

        } else {
            console.log("focus: Table not found. ", tableName);
            alert("focus: Table not found. ", tableName);
        } */

    }

    /* This function uses the XMLHttpRequest object to make an asynchronous GET request to the specified URL ('scripts/php/relational_viewer/get_tables.php'). The onreadystatechange event is used to handle the response when it's complete. If the request is successful (status 200), it parses the JSON response and calls the init function with the received data.
        You can call fetchTablesData() whenever you want to initiate the AJAX request to retrieve the data from your PHP script. */
    function fetchTablesData() {
        console.log("Fetching db tables data...");

        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Configure it: GET-request for the specified URL
        xhr.open('GET', 'scripts/php/relational_viewer/get_tables.php', true);

        // Define the callback function to handle the response
        xhr.onreadystatechange = function () {
            // Check if the request is complete and successful (status 200)
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Parse the JSON response
                var tablesData = JSON.parse(xhr.responseText);

                // store tables data in local storage
                localStorage.setItem("tablesData", JSON.stringify(tablesData));

                console.log("Table Data returned: ");
                console.log(tablesData);

                // count number of tables in tablesData
                let numTables = 0;
                tablesData.forEach(table => {
                    numTables++;
                });

                // count number of relationships in tablesData
                let numRelationships = 0;
                tablesData.forEach(table => {
                    numRelationships += table.relatedTables.length;
                });

                // create unordered list of tables in tables-list div
                let tablesList = `<ul id="db-tables-list" class="list-group d-grid gap-2 p-0">
                    <li class="list-group-item d-flex gap-2 rounded-4 p-3 mt-0 text-truncate bg-white text-primary" 
                        style="direction:ltr;">
                        <span class="material-icons material-icons-outlined align-middle" style="font-size: 60px !important;">
                            account_tree
                        </span>
                        <h3 class="text-start fs-5 m-0 d-grid gap-2" style="direction: ltr">
                            <span>Tables: [ ${numTables} ]</span>
                            <span>Relationships: [ ${numRelationships} ]</span>
                        </h3>
                    </li>`;
                tablesData.forEach(table => {
                    tablesList += `<li id="dbtbl-${table.tableName}" class="list-group-item list-group-item-action rounded-4 m-0 p-0 text-truncate dbtbl-list-item text-bg-primary d-grid shadow">
                            <button class="btn btn-primary btn-sm dbtbl-list-btn rounded-4 fs-5 p-4 fw-bold rounded-4 d-flex justify-content-between" type="button" id="focus-table-${table.tableName}-btn" onclick="">
                                <span> ${table.tableName} </span>
                                <span class="material-icons material-icons-round align-middle"> face </span>
                            </button>
                        </li>`;
                });

                tablesList += "</ul>";
                document.getElementById("tables-list").innerHTML = tablesList;

                // Do something with the data (e.g., pass it to the init function)
                init(tablesData);

                // add event listener to each table list item
                let isClickListenerAdded = false;
                document.querySelectorAll(".dbtbl-list-btn").forEach(item => {
                    // console.log("Adding event listener to:", item); // debug
                    item.addEventListener("click", function () {
                        console.log("Evt.listen: Table list item clicked! - " + item.id);
                        // get string between "focus-table-" and "-btn" which is the table name
                        let tableName = item.id.substring(12, item.id.length - 4);
                        focusOnTable(tableName);
                    });
                    isClickListenerAdded = true;
                    /*  console.log("Evt.listen: Table list item click listener added! [ " +
                         item.id + " - isClickListenerAdded: " + isClickListenerAdded + "]"); // debug */
                });

                showSnackbar("Database tables data fetched successfully!");
            }
        };

        // Send the request
        xhr.send();
    }

    // show snackbar
    function showSnackbar(message) {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        // Add the "show" class to DIV
        x.className = "show";
        x.innerHTML = message;

        // After 5 seconds, remove the show class from DIV
        setTimeout(function () {
            x.className = x.className.replace("show", "");
        }, 5000);
    }

    // function to set set window zoom to 50%
    function zoomOut() {
        zoom = 0.5;
        updateCanvasSize();
    }

    // set window zoom to 50% on page load
    zoomOut();

    // this function calls init() with the actual db data
    fetchTablesData();

    // Example data from an AJAX call (replace this with your actual data)
    /* const exampleTableData = [
        {
            tableName: 'Table1',
            columns: [
                { name: 'Column1', type: 'INT', isPrimaryKey: true },
                { name: 'Column2', type: 'VARCHAR(255)', isPrimaryKey: false },
                { name: 'Column3', type: 'DATE', isPrimaryKey: false }
            ],
            relatedTables: ['Table2', 'Table3'],
            primaryKey: true
        },
        {
            tableName: 'Table2',
            columns: [
                { name: 'ColumnA', type: 'VARCHAR(100)', isPrimaryKey: false },
                { name: 'ColumnB', type: 'DECIMAL(10,2)', isPrimaryKey: true },
                { name: 'ColumnC', type: 'TEXT', isPrimaryKey: false }
            ],
            relatedTables: ['Table1'],
            primaryKey: false
        },
        {
            tableName: 'Table3',
            columns: [
                { name: 'ColumnX', type: 'VARCHAR(50)', isPrimaryKey: true },
                { name: 'ColumnY', type: 'INT', isPrimaryKey: false },
                { name: 'ColumnZ', type: 'DATE', isPrimaryKey: false }
            ],
            relatedTables: ['Table1'],
            primaryKey: false
        },
        // Add more tables as needed
    ]; 
    init(exampleTableData); // Replace this with a call to fetchTablesData()*/
});
