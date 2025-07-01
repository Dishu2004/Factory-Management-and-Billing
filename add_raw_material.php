<?php
include 'connection.php';

$data = json_decode(file_get_contents("php://input"));
$name = $data->name;
$quantity = $data->quantity;
$price = $data->price;
$date = $data->date;

$sql = "INSERT INTO raw_materials (material_name, quantity, price, date) VALUES ('$name', $quantity, $price, '$date')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}

$conn->close();
?>
