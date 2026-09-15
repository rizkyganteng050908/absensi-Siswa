<?php
include "config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

/* TAMBAH SISWA */
if (isset($_POST['tambah'])) {

    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $kelas = intval($_POST['kelas_id']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn,
        "INSERT INTO siswa
        (nis, nama, jenis_kelamin, kelas_id, alamat)
        VALUES
        ('$nis','$nama','$jk','$kelas','$alamat')"
    );

    header("Location: siswa.php");
    exit;
}

/* HAPUS SISWA */
if (isset($_GET['hapus'])) {

    $id = intval($_GET['hapus']);

    mysqli_query($conn,
        "DELETE FROM siswa WHERE id=$id"
    );

    header("Location: siswa.php");
    exit;
}

$kelas = mysqli_query($conn,
    "SELECT * FROM kelas ORDER BY nama_kelas"
);
?>

<!DOCTYPE html>
<html>

<head>

<title>Data Siswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<a href="dashboard.php"
class="btn btn-secondary mb-3">
← Dashboard
</a>

<h2>👨‍🎓 Data Siswa</h2>

<div class="card mt-3">

<div class="card-header bg-primary text-white">
Tambah Siswa
</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>NIS</label>

<input type="text"
name="nis"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Nama Siswa</label>

<input type="text"
name="nama"
class="form-control"
required>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label>Jenis Kelamin</label>

<select name="jenis_kelamin"
class="form-control"
required>

<option value="">-- Pilih --</option>
<option value="L">Laki-laki</option>
<option value="P">Perempuan</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Kelas</label>

<select name="kelas_id"
class="form-control"
required>

<option value="">
-- Pilih Kelas --
</option>

<?php while ($k = mysqli_fetch_assoc($kelas)): ?>

<option value="<?= $k['id'] ?>">
<?= htmlspecialchars($k['nama_kelas']) ?>
</option>

<?php endwhile; ?>

</select>

</div>

</div>

<div class="mb-3">

<label>Alamat</label>

<textarea name="alamat"
class="form-control"></textarea>

</div>

<button name="tambah"
class="btn btn-primary">

+ Tambah Siswa

</button>

</form>

</div>
</div>


<div class="card mt-4">

<div class="card-header">
Daftar Siswa
</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead>

<tr>

<th>No</th>
<th>NIS</th>
<th>Nama</th>
<th>JK</th>
<th>Kelas</th>
<th>Aksi</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

$data = mysqli_query($conn,

"SELECT siswa.*, kelas.nama_kelas

FROM siswa

LEFT JOIN kelas
ON siswa.kelas_id = kelas.id

ORDER BY siswa.nama"

);

while ($row = mysqli_fetch_assoc($data)):

?>

<tr>

<td><?= $no++ ?></td>

<td><?= htmlspecialchars($row['nis']) ?></td>

<td><?= htmlspecialchars($row['nama']) ?></td>

<td><?= $row['jenis_kelamin'] ?></td>

<td>
<?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
</td>

<td>

<a href="?hapus=<?= $row['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Hapus siswa ini?')">

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

</div>

</body>
</html>