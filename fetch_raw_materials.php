<?php
include 'connection.php';

$query = "SELECT * FROM raw_materials";
$result = $conn->query($query);

$materials = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}

echo json_encode($materials);
$conn->close();
?>
