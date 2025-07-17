<?php

require_once 'db_connect.php'; // Memastikan koneksi database tersedia

header("Content-Type: application/json; charset=UTF-8"); // Mengatur header respons sebagai JSON
session_start(); // Memulai sesi untuk mengelola status login

$method = $_SERVER['REQUEST_METHOD']; // Mendapatkan metode request (GET, POST, dll.)

switch ($method) {
    case 'POST':
        $action = isset($_POST['action']) ? $_POST['action'] : ''; // Mendapatkan aksi yang diminta (register, login, logout)
        if ($action === 'register') {
            handleRegister($conn); // Panggil fungsi untuk menangani pendaftaran
        } elseif ($action === 'login') {
            handleLogin($conn); // Panggil fungsi untuk menangani login
        } elseif ($action === 'logout') {
            handleLogout(); // Panggil fungsi untuk menangani logout
        } else {
            echo json_encode(["success" => false, "message" => "Aksi tidak valid."]);
        }
        break;
    case 'GET':
        // Cek status login saat ini
        if (isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role'])) {
            echo json_encode(["success" => true, "isLoggedIn" => true, "username" => $_SESSION['username'], "role" => $_SESSION['role']]);
        } else {
            echo json_encode(["success" => true, "isLoggedIn" => false]);
        }
        break;
    default:
        echo json_encode(["success" => false, "message" => "Metode request tidak didukung."]);
        break;
}

$conn->close(); // Menutup koneksi database

/**
 * Menangani proses pendaftaran pengguna baru (admin).
 * @param mysqli $conn Objek koneksi database.
 */
function handleRegister($conn) {
    $username = $conn->real_escape_string($_POST['username']); // Sanitasi username
    $password = $_POST['password']; // Password plain text dari form

    // Validasi dasar input
    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Username dan password tidak boleh kosong."]);
        return;
    }

    // Periksa apakah username sudah ada di database
    $sql_check = "SELECT id FROM users WHERE username = '$username'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username sudah terdaftar."]);
        return;
    }

    // Hash password sebelum menyimpannya ke database untuk keamanan
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan data pengguna baru ke tabel users
    $sql_insert = "INSERT INTO users (username, password_hash, role) VALUES ('$username', '$password_hash', 'admin')";
    if ($conn->query($sql_insert) === TRUE) {
        echo json_encode(["success" => true, "message" => "Registrasi berhasil. Silakan login."]);
    } else {
        echo json_encode(["success" => false, "message" => "Registrasi gagal: " . $conn->error]);
    }
}

/**
 * Menangani proses login pengguna (admin).
 * @param mysqli $conn Objek koneksi database.
 */
function handleLogin($conn) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password_hash, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password yang dimasukkan dengan hash di database
        if (password_verify($password, $user['password_hash'])) {
            // Set variabel sesi jika login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(["success" => true, "message" => "Login berhasil!", "username" => $user['username'], "role" => $user['role']]);
        } else {
            echo json_encode(["success" => false, "message" => "Password salah."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Username tidak ditemukan."]);
    }
}

/**
 * Menangani proses logout pengguna.
 */
function handleLogout() {
    session_unset();
    session_destroy();
    echo json_encode(["success" => true, "message" => "Logout berhasil."]);
}
?>
