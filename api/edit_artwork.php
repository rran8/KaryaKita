<?php
header("Content-Type: application/json; charset=UTF-8");
require_once 'db_connect.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
$student_name = isset($_POST['student_name']) ? $conn->real_escape_string($_POST['student_name']) : '';
$student_class = isset($_POST['student_class']) ? $conn->real_escape_string($_POST['student_class']) : '';
$category_slug = isset($_POST['category_slug']) ? $conn->real_escape_string($_POST['category_slug']) : '';
$description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';

if ($id > 0 && $title && $student_name && $category_slug) {
    // Ambil nama kategori dari slug
    $catRes = $conn->query("SELECT name FROM categories WHERE slug = '$category_slug' LIMIT 1");
    $category_name = '';
    if ($catRes && $catRes->num_rows > 0) {
        $row = $catRes->fetch_assoc();
        $category_name = $conn->real_escape_string($row['name']);
    }
    $sql = "UPDATE artworks SET title='$title', student_name='$student_name', student_class='$student_class', category_slug='$category_slug', category_name='$category_name', description='$description' WHERE id=$id";
    $conn->query($sql);
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Data tidak valid"]);
}
$conn->close();
?>