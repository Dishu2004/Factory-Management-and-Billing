<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the database connection
include 'connection.php';

// Initialize variables for success and error messages
$success = '';
$error = '';

// If the form is submitted, save city data to the database
if (isset($_POST['submit'])) {
    $city_name = trim($_POST['city_name']);
    $city_year = intval($_POST['city_year']);

    // Prepare the SQL statement to prevent SQL injection
    if ($stmt = $conn->prepare("INSERT INTO cities (name, year_established) VALUES (?, ?)")) {
        $stmt->bind_param("si", $city_name, $city_year);

        if ($stmt->execute()) {
            $success = "City added successfully!";
        } else {
            $error = "Error adding city: " . $conn->error;
        }
        // Close the statement
        $stmt->close();
    } else {
        $error = "Database query error: " . $conn->error;
    }
}

// Fetch all cities from the database to display
$cities = [];
$result = $conn->query("SELECT * FROM cities ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'nav.php'; ?>

    <!-- Main Content -->
    <div class="dashboard-content">
        <h1>Add City</h1>

        <!-- Display success or error message -->
        <?php if ($success): ?>
            <div style="color: green;"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div style="color: red;"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Form to add city -->
        <form method="POST" action="city.php">
            <label for="city_name">City Name</label>
            <input type="text" name="city_name" id="city_name" required>

            <label for="city_year">Year Established</label>
            <input type="number" name="city_year" id="city_year" required>

            <input type="submit" name="submit" value="Add City">
        </form>

        <!-- Display Added Cities -->
        <h2>Added Cities</h2>
        <table>
            <thead>
                <tr>
                    <th>City Name</th>
                    <th>Year Established</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cities)): ?>
                    <?php foreach ($cities as $city): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($city['name']); ?></td>
                            <td><?php echo htmlspecialchars($city['year_established']); ?></td>
                            <td><?php echo htmlspecialchars($city['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No cities added yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
