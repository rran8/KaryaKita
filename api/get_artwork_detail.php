<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'db_connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID karya tidak valid."]);
    exit;
}

$result = $conn->query("SELECT * FROM artworks WHERE id = $id LIMIT 1");
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode(["success" => true, "artwork" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Karya tidak ditemukan."]);
}
$conn->close();
?>