<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'db_connect.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id > 0) {
    $conn->query("DELETE FROM artworks WHERE id=$id");
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
}
$conn->close();
?>