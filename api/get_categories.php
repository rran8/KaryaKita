<?php
// api/get_categories.php

require_once 'db_connect.php';

$sql = "SELECT name, slug FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

echo json_encode(["success" => true, "categories" => $categories]);

$conn->close();
?>
