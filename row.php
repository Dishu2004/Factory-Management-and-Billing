<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raw Material Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your external CSS -->
</head>
<body>

    <?php include 'nav.php'; ?> <!-- Include Sidebar -->
    <?php include 'connection.php'; ?> <!-- Include your database connection -->

    <div class="content">
        <h1>Raw Material Management</h1>

        <div class="form-container">
            <form id="raw-material-form" onsubmit="addRawMaterial(event)">
                <input type="text" id="material-name" placeholder="Raw Material Name" required>
                <input type="number" id="quantity" placeholder="Quantity" required>
                <input type="number" id="price" placeholder="Price" required>
                <input type="date" id="date" required>
                <button type="submit">Submit</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Raw Material Records</h2>
            <table class="table" id="raw-materials-table">
                <thead>
                    <tr>
                        <th>Raw Material Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be dynamically populated via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
    let currentEditId = null; // To store the ID of the material being edited

    // Fetch existing raw materials from the database on page load
    window.onload = function() {
        fetchRawMaterials();
    };

    function fetchRawMaterials() {
        fetch('fetch_raw_materials.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('raw-materials-table').getElementsByTagName('tbody')[0];
                tableBody.innerHTML = ''; // Clear existing rows
                data.forEach(material => {
                    const newRow = tableBody.insertRow();
                    newRow.innerHTML = `
                        <td>${material.material_name}</td>
                        <td>${material.quantity}</td>
                        <td>${material.price}</td>
                        <td>${material.date}</td>
                        <td>
                            <button class="edit-btn" onclick="editRawMaterial(this, ${material.id})">Edit</button>
                            <button class="delete-btn" onclick="deleteRawMaterial(this, ${material.id})">Delete</button>
                        </td>
                    `;
                });
            });
    }

    function addRawMaterial(event) {
        event.preventDefault(); // Prevent form submission

        const name = document.getElementById('material-name').value;
        const quantity = document.getElementById('quantity').value;
        const price = document.getElementById('price').value;
        const date = document.getElementById('date').value;

        if (currentEditId) {
            // If editing, update the material
            fetch('update_raw_material.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: currentEditId, name, quantity, price, date })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchRawMaterials(); // Refresh the table
                    document.getElementById('raw-material-form').reset(); // Clear the form inputs
                    currentEditId = null; // Reset the edit ID
                } else {
                    alert('Failed to update raw material.');
                }
            });
        } else {
            // If adding, create a new material
            fetch('add_raw_material.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, quantity, price, date })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchRawMaterials(); // Refresh the table
                    document.getElementById('raw-material-form').reset(); // Clear the form inputs
                } else {
                    alert('Failed to add raw material.');
                }
            });
        }
    }

    function editRawMaterial(button, id) {
        const row = button.parentElement.parentElement;
        const cells = row.getElementsByTagName('td');

        // Populate the form with the current row data for editing
        document.getElementById('material-name').value = cells[0].innerText;
        document.getElementById('quantity').value = cells[1].innerText;
        document.getElementById('price').value = cells[2].innerText;
        document.getElementById('date').value = cells[3].innerText;

        // Store the ID of the material being edited
        currentEditId = id; 

        // Change the form submission handler to update the material
        document.getElementById('raw-material-form').onsubmit = function(event) {
            addRawMaterial(event);
        };
    }

    function deleteRawMaterial(button, id) {
        if (confirm('Are you sure you want to delete this raw material?')) {
            fetch('delete_raw_material.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = button.parentElement.parentElement;
                    row.parentElement.removeChild(row);
                } else {
                    alert('Failed to delete raw material.');
                }
            });
        }
    }
</script>

</body>
</html>
