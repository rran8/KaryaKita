<?php
// api/admin_api.php

require_once 'db_connect.php';

header("Content-Type: application/json; charset=UTF-8");
session_start(); // Start session to check admin login

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Akses ditolak. Hanya admin yang diizinkan."]);
    $conn->close();
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {
    case 'get_all_artworks':
        getAllArtworks($conn);
        break;
    case 'approve_artwork':
        approveArtwork($conn);
        break;
    case 'reject_artwork':
        rejectArtwork($conn);
        break;
    case 'delete_artwork':
        deleteArtwork($conn);
        break;
    case 'add_category':
        addCategory($conn);
        break;
    case 'get_all_categories':
        getAllCategories($conn);
        break;
    case 'delete_category':
        deleteCategory($conn);
        break;
    case 'update_artwork':
        updateArtwork($conn);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Aksi tidak dikenal."]);
        break;
}

$conn->close();

function getAllArtworks($conn) {
    $sql = "SELECT id, title, student_name, student_class, category_name, media_type, media_url, upload_date, views, status FROM artworks ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $artworks = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $artworks[] = $row;
        }
    }
    echo json_encode(["success" => true, "artworks" => $artworks]);
}

function approveArtwork($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $sql = "UPDATE artworks SET status = 'approved' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Karya berhasil disetujui."]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menyetujui karya: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID karya tidak valid."]);
    }
}

function rejectArtwork($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $sql = "UPDATE artworks SET status = 'rejected' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Karya berhasil ditolak."]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menolak karya: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID karya tidak valid."]);
    }
}

function deleteArtwork($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $sql = "DELETE FROM artworks WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Karya berhasil dihapus."]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menghapus karya: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID karya tidak valid."]);
    }
}

function addCategory($conn) {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name)); // Generate slug from name

    if (empty($name)) {
        echo json_encode(["success" => false, "message" => "Nama kategori tidak boleh kosong."]);
        return;
    }

    // Check if category already exists
    $sql_check = "SELECT id FROM categories WHERE slug = '$slug'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Kategori dengan nama ini sudah ada."]);
        return;
    }

    $sql = "INSERT INTO categories (name, slug) VALUES ('$name', '$slug')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Kategori berhasil ditambahkan.", "category" => ["name" => $name, "slug" => $slug]]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menambahkan kategori: " . $conn->error]);
    }
}

function getAllCategories($conn) {
    $sql = "SELECT id, name, slug FROM categories ORDER BY name ASC";
    $result = $conn->query($sql);

    $categories = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    echo json_encode(["success" => true, "categories" => $categories]);
}

function deleteCategory($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        // Optional: Check if any artworks are linked to this category before deleting
        // If so, you might want to prevent deletion or reassign artworks to a default category

        $sql = "DELETE FROM categories WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Kategori berhasil dihapus."]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menghapus kategori: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID kategori tidak valid."]);
    }
}

function updateArtwork($conn) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $student_class = isset($_POST['student_class']) ? $conn->real_escape_string($_POST['student_class']) : '';
    $category_slug = $conn->real_escape_string($_POST['category_slug']);
    $category_name = $conn->real_escape_string($_POST['category_name']);
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $media_type = $conn->real_escape_string($_POST['media_type']);
    $media_url = $conn->real_escape_string($_POST['media_url']);
    $status = $conn->real_escape_string($_POST['status']);


    if ($id <= 0 || empty($title) || empty($student_name) || empty($category_slug) || empty($media_type) || empty($media_url) || empty($status)) {
        echo json_encode(["success" => false, "message" => "Data yang wajib diisi tidak lengkap atau ID tidak valid."]);
        return;
    }

    $sql = "UPDATE artworks SET
                title = '$title',
                student_name = '$student_name',
                student_class = '$student_class',
                category_slug = '$category_slug',
                category_name = '$category_name',
                description = '$description',
                media_type = '$media_type',
                media_url = '$media_url',
                status = '$status'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Karya berhasil diperbarui."]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal memperbarui karya: " . $conn->error]);
    }
}
?>
