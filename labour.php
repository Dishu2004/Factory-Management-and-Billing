<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
include 'connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        // Insert new labor record
        $labor_number = $_POST['labor_number'];
        $date = $_POST['date'];
        $amount = $_POST['amount'];

        $stmt = $conn->prepare("INSERT INTO labor_records (labor_number, date, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $labor_number, $date, $amount);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Delete labor record
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM labor_records WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all labor records
$result = $conn->query("SELECT * FROM labor_records");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your external CSS -->
</head>
<body>

    <?php include 'nav.php'; ?> <!-- Include Sidebar -->

    <div class="content">
        <h1>Labor Management</h1>

        <form id="labor-form" method="post">
            <div class="form-group">
                <label for="labor-number">Number of Laborers:</label>
                <input type="number" id="labor-number" name="labor_number" required>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
            </div>
            <input type="hidden" name="action" value="add">
            <button type="submit" class="submit-btn">Submit</button>
        </form>

        <div class="table-container">
            <table class="table" id="labor-table">
                <thead>
                    <tr>
                        <th>No. of Laborers</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['labor_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars('$' . number_format($row['amount'], 2)); ?></td>
                        <td>
                            <button class="edit-btn" onclick="editRecord(<?php echo $row['id']; ?>)">Edit</button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editRecord(id) {
            // Fetch record data and populate the form (you can implement AJAX for this)
            alert('Edit function for ID ' + id + ' needs to be implemented.');
        }
    </script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
