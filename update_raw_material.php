<?php
include 'connection.php';

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;
$name = $data->name;
$quantity = $data->quantity;
$price = $data->price;
$date = $data->date;

$sql = "UPDATE raw_materials SET material_name='$name', quantity=$quantity, price=$price, date='$date' WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}

$conn->close();
?>
