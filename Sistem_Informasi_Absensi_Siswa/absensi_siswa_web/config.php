<?php
// Konfigurasi database. Ubah jika username/password MySQL berbeda.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_absensi_siswa";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function login_required() {
    if (empty($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}
function admin_required() {
    login_required();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        die("Akses hanya untuk Admin.");
    }
}
function flash($type, $message) {
    $_SESSION['flash'] = [$type, $message];
}
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$type, $message] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert '.e($type).'">'.e($message).'</div>';
    }
}
