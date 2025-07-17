<?php
// api/add_artwork.php
header("Content-Type: application/json; charset=UTF-8");

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $student_class = isset($_POST['student_class']) ? $conn->real_escape_string($_POST['student_class']) : '';
    $category_slug = $conn->real_escape_string($_POST['category_slug']);

    // Ambil nama kategori dari database berdasarkan slug
    $category_name = '';
    $catRes = $conn->query("SELECT name FROM categories WHERE slug = '$category_slug' LIMIT 1");
    if ($catRes && $catRes->num_rows > 0) {
        $row = $catRes->fetch_assoc();
        $category_name = $conn->real_escape_string($row['name']);
    } else {
        echo json_encode(["success" => false, "message" => "Kategori tidak ditemukan."]);
        $conn->close();
        exit();
    }

    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $media_type = isset($_POST['media_type']) ? $conn->real_escape_string($_POST['media_type']) : '';
    $media_url = isset($_POST['media_url']) ? $conn->real_escape_string($_POST['media_url']) : '';

    // Validasi dasar
    if (empty($title) || empty($student_name) || empty($category_slug) || empty($media_type)) {
        echo json_encode(["success" => false, "message" => "Data yang wajib diisi tidak lengkap."]);
        $conn->close();
        exit();
    }

    // Proses upload file jika ada
    if ($media_type === 'image' || $media_type === 'pdf') {
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['media_file']['name']);
            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $maxSize = ($media_type === 'image') ? 10 * 1024 * 1024 : 100 * 1024 * 1024; // 10MB/100MB

            // Validasi tipe dan ukuran file
            if (
                ($media_type === 'image' && !in_array($fileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) ||
                ($media_type === 'pdf' && $fileType !== 'pdf')
            ) {
                echo json_encode(["success" => false, "message" => "Tipe file tidak didukung."]);
                $conn->close();
                exit();
            }
            if ($_FILES['media_file']['size'] > $maxSize) {
                echo json_encode(["success" => false, "message" => "Ukuran file terlalu besar."]);
                $conn->close();
                exit();
            }

            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $targetFile)) {
                $media_url = 'uploads/' . $fileName;
            } else {
                echo json_encode(["success" => false, "message" => "Gagal upload file media."]);
                $conn->close();
                exit();
            }
        } else {
            echo json_encode(["success" => false, "message" => "File media tidak ditemukan atau error."]);
            $conn->close();
            exit();
        }
    } elseif ($media_type === 'youtube_link') {
        $media_url = $conn->real_escape_string($_POST['media_url']);
    }

    $sql = "INSERT INTO artworks (title, student_name, student_class, category_slug, category_name, description, media_type, media_url, status)
            VALUES ('$title', '$student_name', '$student_class', '$category_slug', '$category_name', '$description', '$media_type', '$media_url', 'pending')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Karya berhasil diunggah!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode request tidak valid."]);
}

$conn->close();
?>
