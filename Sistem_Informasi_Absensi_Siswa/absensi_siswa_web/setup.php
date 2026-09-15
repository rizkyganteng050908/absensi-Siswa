<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_absensi_siswa";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) die("MySQL belum aktif / koneksi gagal: ".$conn->connect_error);
$conn->set_charset("utf8mb4");
$conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($db);

$queries = [
"CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','guru') NOT NULL DEFAULT 'guru',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)",
"CREATE TABLE IF NOT EXISTS kelas (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nama_kelas VARCHAR(50) NOT NULL,
 wali_kelas VARCHAR(100) DEFAULT NULL
)",
"CREATE TABLE IF NOT EXISTS siswa (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nis VARCHAR(30) NOT NULL UNIQUE,
 nama VARCHAR(100) NOT NULL,
 jk ENUM('L','P') NOT NULL,
 kelas_id INT NOT NULL,
 foto VARCHAR(255) DEFAULT NULL,
 FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE RESTRICT
)",
"CREATE TABLE IF NOT EXISTS status_presensi (
 id INT AUTO_INCREMENT PRIMARY KEY,
 kode VARCHAR(10) NOT NULL UNIQUE,
 nama_status VARCHAR(30) NOT NULL,
 warna VARCHAR(20) DEFAULT '#2563eb'
)",
"CREATE TABLE IF NOT EXISTS presensi (
 id INT AUTO_INCREMENT PRIMARY KEY,
 siswa_id INT NOT NULL,
 tanggal DATE NOT NULL,
 jam_pelajaran VARCHAR(50) DEFAULT 'Harian',
 status_id INT NOT NULL,
 keterangan VARCHAR(255) DEFAULT NULL,
 created_by INT DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unik_presensi (siswa_id,tanggal,jam_pelajaran),
 FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
 FOREIGN KEY (status_id) REFERENCES status_presensi(id) ON DELETE RESTRICT,
 FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
)",
"CREATE TABLE IF NOT EXISTS pengajuan_surat (
 id INT AUTO_INCREMENT PRIMARY KEY,
 siswa_id INT NOT NULL,
 tanggal_mulai DATE NOT NULL,
 tanggal_selesai DATE NOT NULL,
 jenis ENUM('Sakit','Izin') NOT NULL,
 alasan TEXT NOT NULL,
 file_surat VARCHAR(255) DEFAULT NULL,
 status ENUM('Menunggu','Disetujui','Ditolak') DEFAULT 'Menunggu',
 catatan_verifikasi TEXT DEFAULT NULL,
 diajukan_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 diverifikasi_at DATETIME DEFAULT NULL,
 diverifikasi_oleh INT DEFAULT NULL,
 FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
 FOREIGN KEY (diverifikasi_oleh) REFERENCES users(id) ON DELETE SET NULL
)"
];

foreach ($queries as $q) {
    if (!$conn->query($q)) die("Gagal membuat tabel: ".$conn->error);
}

$defaults = [
 ["H","Hadir","#16a34a"],
 ["S","Sakit","#f59e0b"],
 ["I","Izin","#2563eb"],
 ["A","Alpa","#dc2626"]
];
$stmt = $conn->prepare("INSERT IGNORE INTO status_presensi(kode,nama_status,warna) VALUES(?,?,?)");
foreach ($defaults as $d) { $stmt->bind_param("sss",$d[0],$d[1],$d[2]); $stmt->execute(); }

$check = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
if (!$check) {
    $hash = password_hash("admin123", PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users(nama,username,password,role) VALUES('Administrator','admin',?,'admin')");
    $stmt->bind_param("s",$hash);
    $stmt->execute();
}
$kelasCount = $conn->query("SELECT COUNT(*) c FROM kelas")->fetch_assoc()['c'];
if (!$kelasCount) {
    $conn->query("INSERT INTO kelas(nama_kelas,wali_kelas) VALUES
        ('X RPL 1','Wali Kelas X RPL 1'),
        ('XI RPL 1','Wali Kelas XI RPL 1'),
        ('XII RPL 1','Wali Kelas XII RPL 1')");
}
$conn->close();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Setup Berhasil</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<div class="center"><div class="card setup">
<h1>✅ Database Berhasil Dibuat</h1>
<p>Sistem sudah membuat database <b>db_absensi_siswa</b>, tabel, status presensi, akun admin, dan contoh kelas.</p>
<div class="info"><b>Login Admin</b><br>Username: <code>admin</code><br>Password: <code>admin123</code></div>
<a class="btn" href="login.php">Masuk ke Sistem</a>
<p class="muted">Demi keamanan, hapus atau rename file <b>setup.php</b> setelah instalasi selesai.</p>
</div></div></body></html>
