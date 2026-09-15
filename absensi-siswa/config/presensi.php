<?php
include "config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$kelas_id = $_GET['kelas_id'] ?? '';

/* SIMPAN PRESENSI */
if (isset($_POST['simpan'])) {

    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $kelas_id = $_POST['kelas_id'];

    foreach ($_POST['status'] as $siswa_id => $status_id) {

        $siswa_id = intval($siswa_id);
        $status_id = intval($status_id);

        $cek = mysqli_query($conn,
            "SELECT id FROM presensi
             WHERE siswa_id=$siswa_id
             AND tanggal='$tanggal'
             AND jam='$jam'"
        );

        if (mysqli_num_rows($cek) > 0) {

            mysqli_query($conn,
                "UPDATE presensi
                 SET status_id=$status_id
                 WHERE siswa_id=$siswa_id
                 AND tanggal='$tanggal'
                 AND jam='$jam'"
            );

        } else {

            mysqli_query($conn,
                "INSERT INTO presensi
                (siswa_id,tanggal,jam,status_id)
                VALUES
                ($siswa_id,'$tanggal','$jam',$status_id)"
            );

        }
    }

    echo "<script>
        alert('Presensi berhasil disimpan!');
        window.location='presensi.php';
    </script>";

    exit;
}

$kelas = mysqli_query($conn,
    "SELECT * FROM kelas ORDER BY nama_kelas"
);

$siswa = [];

if ($kelas_id != '') {

    $query = mysqli_query($conn,

        "SELECT * FROM siswa
         WHERE kelas_id=" . intval($kelas_id) . "
         ORDER BY nama"

    );

    while ($row = mysqli_fetch_assoc($query)) {
        $siswa[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Presensi Siswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<a href="dashboard.php"
class="btn btn-secondary mb-3">
← Dashboard
</a>

<h2>📝 Presensi Siswa</h2>

<div class="card mt-3">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-4">

<label>Tanggal</label>

<input type="date"
name="tanggal"
value="<?= $tanggal ?>"
class="form-control">

</div>

<div class="col-md-4">

<label>Kelas</label>

<select name="kelas_id"
class="form-control"
required>

<option value="">
-- Pilih Kelas --
</option>

<?php while ($k = mysqli_fetch_assoc($kelas)): ?>

<option value="<?= $k['id'] ?>"
<?= $kelas_id == $k['id'] ? 'selected' : '' ?>>

<?= htmlspecialchars($k['nama_kelas']) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="col-md-4">

<label>&nbsp;</label>

<button class="btn btn-primary w-100">
Tampilkan Siswa
</button>

</div>

</div>

</form>

</div>
</div>


<?php if (count($siswa) > 0): ?>

<div class="card mt-4">

<div class="card-header bg-primary text-white">

Input Presensi

</div>

<div class="card-body">

<form method="POST">

<input type="hidden"
name="tanggal"
value="<?= $tanggal ?>">

<input type="hidden"
name="kelas_id"
value="<?= $kelas_id ?>">


<div class="mb-3">

<label>Jam Pelajaran</label>

<select name="jam"
class="form-control"
required>

<option value="07:00-08:00">
07:00 - 08:00
</option>

<option value="08:00-09:00">
08:00 - 09:00
</option>

<option value="09:00-10:00">
09:00 - 10:00
</option>

<option value="10:00-11:00">
10:00 - 11:00
</option>

<option value="11:00-12:00">
11:00 - 12:00
</option>

</select>

</div>


<table class="table table-bordered">

<thead>

<tr>

<th>No</th>
<th>NIS</th>
<th>Nama</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

foreach ($siswa as $s):

?>

<tr>

<td><?= $no++ ?></td>

<td>
<?= htmlspecialchars($s['nis']) ?>
</td>

<td>
<?= htmlspecialchars($s['nama']) ?>
</td>

<td>

<select name="status[<?= $s['id'] ?>]"
class="form-control">

<option value="1">
Hadir
</option>

<option value="2">
Sakit
</option>

<option value="3">
Izin
</option>

<option value="4">
Alpa
</option>

</select>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<button name="simpan"
class="btn btn-success">

💾 Simpan Presensi

</button>

</form>

</div>
</div>

<?php elseif ($kelas_id != ''): ?>

<div class="alert alert-warning mt-4">

Belum ada siswa pada kelas tersebut.

</div>

<?php endif; ?>

</div>

</body>
</html>