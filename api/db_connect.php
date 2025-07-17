<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Pastikan tidak ada spasi atau karakter di luar tag PHP pembuka dan penutup.
// header() harus dipanggil sebelum ada output ke browser.
header("Access-Control-Allow-Origin: *"); // Izinkan akses dari semua domain (untuk pengembangan)
header("Content-Type: application/json; charset=UTF-8"); // Pastikan respons selalu JSON

$servername = "localhost"; // Ganti jika database Anda di server lain
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "karyakita"; // Nama database yang telah Anda buat

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi dan kirim respons JSON error
    // Pastikan tidak ada output lain di sini sebelum die()
    die(json_encode(["success" => false, "message" => "Koneksi database gagal: " . $conn->connect_error]));
}
// Tidak ada output lain di sini
?>
