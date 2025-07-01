<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS file -->
</head>
<body>

    <?php include 'nav.php'; ?> <!-- Include Sidebar -->
    
    <?php
    // Include the database connection
    include 'connection.php';

    // Initialize variables for success and error messages
    $success = '';
    $error = '';

    // If the form is submitted, save dealer data to the database
    if (isset($_POST['dealer_name'])) {
        $dealer_name = trim($_POST['dealer_name']);
        $city_id = intval($_POST['city']);
        $year = intval($_POST['year']);
        $contact_no = trim($_POST['contact_no']);

        // Prepare the SQL statement to prevent SQL injection
        if ($stmt = $conn->prepare("INSERT INTO dealers (dealer_name, city_id, year, contact_no) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param("ssis", $dealer_name, $city_id, $year, $contact_no);

            if ($stmt->execute()) {
                $success = "Dealer added successfully!";
            } else {
                $error = "Error adding dealer: " . $conn->error;
            }
            // Close the statement
            $stmt->close();
        } else {
            $error = "Database query error: " . $conn->error;
        }
    }

    // Fetch all cities for the dropdown
    $cities = [];
    $cityResult = $conn->query("SELECT * FROM cities ORDER BY name ASC");
    if ($cityResult) {
        while ($cityRow = $cityResult->fetch_assoc()) {
            $cities[] = $cityRow;
        }
    }

    // Fetch all dealers from the database to display
    $dealers = [];
    $dealerResult = $conn->query("SELECT d.*, c.name AS city_name FROM dealers d JOIN cities c ON d.city_id = c.id ORDER BY d.created_at DESC");
    if ($dealerResult) {
        while ($dealerRow = $dealerResult->fetch_assoc()) {
            $dealers[] = $dealerRow;
        }
    }


    // Handle dealer deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($stmt = $conn->prepare("DELETE FROM dealers WHERE id = ?")) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success = "Dealer deleted successfully!";
        } else {
            $error = "Error deleting dealer: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Database query error: " . $conn->error;
    }
}

    // Close the database connection
    $conn->close();
    ?>

    <div class="content">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" placeholder="Search Dealer..." id="search-dealer" onkeyup="filterDealers()" />
        </div>

        <div class="container">
            <!-- Dealer Form -->
            <div class="form-container">
                <h2>Add New Dealer</h2>
                <form action="dealer.php" method="POST">
                    <div class="input-box">
                        <input type="text" name="dealer_name" required>
                        <label>Dealer Name</label>
                    </div>
                    <div class="input-box">
                        <label for="city">City</label><br><br>
                        <select name="city" required>
                            <option value="">Select a city</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city['id']); ?>">
                                    <?php echo htmlspecialchars($city['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-box">
                        <input type="number" name="year" required>
                        <label>Year</label>
                    </div>
                    <div class="input-box">
                        <input type="text" name="contact_no" required>
                        <label>Contact No.</label>
                    </div>
                    <input type="submit" class="submit-btn" value="Add Dealer">
                </form>
                <?php if ($success): ?>
                    <div style="color: green;"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div style="color: red;"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>

            <!-- Dealers List Table -->
            <div class="table-container">
                <h2>Dealers List</h2>
                <div class="table-wrapper">
                    <table class="table" id="dealers-table">
                        <thead>
                            <tr>
                                <th>Dealer Name</th>
                                <th>City</th>
                                <th>Year</th>
                                <th>Contact No.</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php if (!empty($dealers)): ?>
        <?php foreach ($dealers as $dealer): ?>
            <tr>
                <td><?php echo htmlspecialchars($dealer['dealer_name']); ?></td>
                <td><?php echo htmlspecialchars($dealer['city_name']); ?></td>
                <td><?php echo htmlspecialchars($dealer['year']); ?></td>
                <td><?php echo htmlspecialchars($dealer['contact_no']); ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($dealer['id']); ?>">
                        <input type="text" name="dealer_name" placeholder="Name" value="<?php echo htmlspecialchars($dealer['dealer_name']); ?>" required>
                        <select name="city" required>
                            <option value="">Select a city</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city['id']); ?>" <?php echo $city['id'] == $dealer['city_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="year" value="<?php echo htmlspecialchars($dealer['year']); ?>" required>
                        <input type="text" name="contact_no" value="<?php echo htmlspecialchars($dealer['contact_no']); ?>" required>
                        <input type="submit" value="Update">
                    </form>
                    
                    <!-- Delete Button Form -->
                    <form method="GET" style="display: inline;">
                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($dealer['id']); ?>">
                        <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this dealer?');">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No dealers added yet.</td>
        </tr>
    <?php endif; ?>
</tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterDealers() {
            const input = document.getElementById('search-dealer');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('dealers-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length - 1; j++) { // Exclude the last column (actions)
                    if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? '' : 'none'; // Show or hide rows based on match
            }
        }
    </script>

</body>
</html>
