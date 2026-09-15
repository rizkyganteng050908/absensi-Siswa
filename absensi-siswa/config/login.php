<?php
include "config/koneksi.php";

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn,
        "SELECT * FROM users
         WHERE username='$username'
         AND password='$password'"
    );

    if (mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);

        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit;

    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Absensi Siswa</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: linear-gradient(135deg,#2563eb,#0f172a);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login {
            background: white;
            width: 350px;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }

        h2 {
            text-align: center;
            color: #2563eb;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
        }

        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<div class="login">

    <h2>📚 Absensi Siswa</h2>

    <?php if ($error): ?>
        <div class="error">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button name="login">
            LOGIN
        </button>

    </form>

</div>

</body>
</html>