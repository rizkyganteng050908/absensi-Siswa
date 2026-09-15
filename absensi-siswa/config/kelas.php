<?php
include "config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

/* TAMBAH KELAS */
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);

    mysqli_query($conn,
        "INSERT INTO kelas (nama_kelas, jurusan)
         VALUES ('$nama', '$jurusan')"
    );

    header("Location: kelas.php");
    exit;
}

/* HAPUS KELAS */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    mysqli_query($conn, "DELETE FROM kelas WHERE id=$id");

    header("Location: kelas.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        ← Dashboard
    </a>

    <h2>🏫 Data Kelas</h2>

    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            Tambah Kelas
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Nama Kelas</label>
                    <input type="text"
                           name="nama_kelas"
                           class="form-control"
                           placeholder="Contoh: X RPL 1"
                           required>
                </div>

                <div class="mb-3">
                    <label>Jurusan</label>
                    <input type="text"
                           name="jurusan"
                           class="form-control"
                           placeholder="Contoh: Rekayasa Perangkat Lunak">
                </div>

                <button name="tambah" class="btn btn-primary">
                    + Tambah Kelas
                </button>

            </form>

        </div>
    </div>

    <div class="card mt-4">

        <div class="card-header">
            Daftar Kelas
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $no = 1;

                $data = mysqli_query($conn,
                    "SELECT * FROM kelas ORDER BY nama_kelas ASC"
                );

                while ($row = mysqli_fetch_assoc($data)):
                ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td>
                        <?= htmlspecialchars($row['nama_kelas']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['jurusan']) ?>
                    </td>

                    <td>

                        <a href="?hapus=<?= $row['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Hapus kelas ini?')">
                            Hapus
                        </a>

                    </td>

                </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>