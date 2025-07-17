<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'db_connect.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($id > 0 && in_array($status, ['pending', 'published', 'declined'])) {
    $conn->query("UPDATE artworks SET status='$status' WHERE id=$id");
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Data tidak valid"]);
}
$conn->close();
?>