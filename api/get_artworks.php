<?php
// api/get_artworks.php

header("Content-Type: application/json; charset=UTF-8");
require_once 'db_connect.php';

$where = "WHERE status = 'published'";
if (isset($_GET['category_slug']) && $_GET['category_slug'] !== '') {
    $slug = $conn->real_escape_string($_GET['category_slug']);
    $where .= " AND category_slug = '$slug'";
}
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
$limitSql = $limit > 0 ? "LIMIT $limit" : "";

$sql = "SELECT id, title, student_name, category_name, media_type, media_url, created_at FROM artworks $where ORDER BY id DESC $limitSql";
$result = $conn->query($sql);

$artworks = [];
while ($row = $result->fetch_assoc()) {
    $artworks[] = $row;
}

echo json_encode(["success" => true, "artworks" => $artworks]);
$conn->close();
?>
