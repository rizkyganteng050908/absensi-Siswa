<?php
require "config.php";
if (!empty($_SESSION['user'])) { header("Location: index.php"); exit; }
$error = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s",$username); $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if ($u && password_verify($password,$u['password'])) {
        $_SESSION['user'] = $u;
        header("Location: index.php"); exit;
    }
    $error = "Username atau password salah.";
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - Absensi Siswa</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-bg"><div class="login-card">
<h1>📚 Absensi Siswa</h1><p class="muted">Sistem Informasi Absensi Siswa Berbasis Web</p>
<?php if($error): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
<form method="post"><label>Username</label><input name="username" required autofocus>
<label>Password</label><input type="password" name="password" required>
<button class="btn full">Login</button></form>
<div class="demo">Demo: <b>admin</b> / <b>admin123</b></div>
</div></body></html>
