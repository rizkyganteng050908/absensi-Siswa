<?php
include "config/koneksi.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$hari_ini = date('Y-m-d');

/* =========================
   TOTAL SISWA
========================= */
$query_siswa = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM siswa"
);

$data_siswa = mysqli_fetch_assoc($query_siswa);
$total_siswa = $data_siswa['total'];


/* =========================
   TOTAL KELAS
========================= */
$query_kelas = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM kelas"
);

$data_kelas = mysqli_fetch_assoc($query_kelas);
$total_kelas = $data_kelas['total'];


/* =========================
   HADIR HARI INI
========================= */
$query_hadir = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM presensi p
     JOIN master_presensi mp ON p.status_id = mp.id
     WHERE p.tanggal = '$hari_ini'
     AND mp.kode = 'H'"
);

$data_hadir = mysqli_fetch_assoc($query_hadir);
$total_hadir = $data_hadir['total'];


/* =========================
   SAKIT HARI INI
========================= */
$query_sakit = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM presensi p
     JOIN master_presensi mp ON p.status_id = mp.id
     WHERE p.tanggal = '$hari_ini'
     AND mp.kode = 'S'"
);

$data_sakit = mysqli_fetch_assoc($query_sakit);
$total_sakit = $data_sakit['total'];


/* =========================
   IZIN HARI INI
========================= */
$query_izin = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM presensi p
     JOIN master_presensi mp ON p.status_id = mp.id
     WHERE p.tanggal = '$hari_ini'
     AND mp.kode = 'I'"
);

$data_izin = mysqli_fetch_assoc($query_izin);
$total_izin = $data_izin['total'];


/* =========================
   ALPA HARI INI
========================= */
$query_alpa = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM presensi p
     JOIN master_presensi mp ON p.status_id = mp.id
     WHERE p.tanggal = '$hari_ini'
     AND mp.kode = 'A'"
);

$data_alpa = mysqli_fetch_assoc($query_alpa);
$total_alpa = $data_alpa['total'];


/* =========================
   TOTAL PENGAJUAN MENUNGGU
========================= */
$query_pengajuan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pengajuan_izin
     WHERE status = 'Menunggu'"
);

$data_pengajuan = mysqli_fetch_assoc($query_pengajuan);
$total_pengajuan = $data_pengajuan['total'];


/* =========================
   PERSENTASE KEHADIRAN
========================= */
$query_total_presensi = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM presensi"
);

$data_total_presensi = mysqli_fetch_assoc($query_total_presensi);
$total_presensi = $data_total_presensi['total'];

if ($total_presensi > 0) {
    $persentase_hadir = round(
        ($total_hadir / $total_presensi) * 100,
        1
    );
} else {
    $persentase_hadir = 0;
}


/* =========================
   DATA PRESENSI TERBARU
========================= */
$presensi_terbaru = mysqli_query(
    $conn,
    "SELECT
        presensi.*,
        siswa.nis,
        siswa.nama,
        kelas.nama_kelas,
        master_presensi.status AS status_presensi,
        master_presensi.kode
     FROM presensi

     JOIN siswa
     ON presensi.siswa_id = siswa.id

     LEFT JOIN kelas
     ON siswa.kelas_id = kelas.id

     JOIN master_presensi
     ON presensi.status_id = master_presensi.id

     ORDER BY presensi.id DESC
     LIMIT 10"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dashboard - Sistem Absensi Siswa</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f1f5f9;
}


/* =========================
   SIDEBAR
========================= */

.sidebar {
    width: 260px;
    min-height: 100vh;
    background: #0f172a;
    position: fixed;
    left: 0;
    top: 0;
    padding: 20px;
    color: white;
}

.logo {
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    padding: 15px 0 30px 0;
    border-bottom: 1px solid #334155;
    margin-bottom: 20px;
}

.logo i {
    color: #60a5fa;
}

.menu-title {
    font-size: 12px;
    color: #94a3b8;
    margin: 20px 10px 10px;
}

.sidebar a {
    display: block;
    color: #cbd5e1;
    text-decoration: none;
    padding: 12px 15px;
    margin-bottom: 5px;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar a i {
    width: 25px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #2563eb;
    color: white;
}


/* =========================
   CONTENT
========================= */

.main {
    margin-left: 260px;
    min-height: 100vh;
}

.topbar {
    background: white;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.topbar h5 {
    margin: 0;
    font-weight: bold;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-icon {
    width: 40px;
    height: 40px;
    background: #2563eb;
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.content {
    padding: 30px;
}


/* =========================
   CARD STATISTIK
========================= */

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    height: 100%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border: none;
}

.stat-card .icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 22px;
    margin-bottom: 15px;
}

.icon-blue {
    background: #dbeafe;
    color: #2563eb;
}

.icon-green {
    background: #dcfce7;
    color: #16a34a;
}

.icon-yellow {
    background: #fef3c7;
    color: #d97706;
}

.icon-red {
    background: #fee2e2;
    color: #dc2626;
}

.icon-purple {
    background: #f3e8ff;
    color: #9333ea;
}

.stat-card h6 {
    color: #64748b;
}

.stat-card h2 {
    font-weight: bold;
}


/* =========================
   DASHBOARD CARD
========================= */

.dashboard-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border: none;
}

.table th {
    background: #f8fafc;
}

.badge-hadir {
    background: #dcfce7;
    color: #15803d;
}

.badge-sakit {
    background: #fef3c7;
    color: #b45309;
}

.badge-izin {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-alpa {
    background: #fee2e2;
    color: #b91c1c;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width: 768px) {

    .sidebar {
        width: 70px;
        padding: 10px;
    }

    .sidebar .menu-text,
    .sidebar .menu-title,
    .logo span {
        display: none;
    }

    .logo {
        font-size: 20px;
    }

    .sidebar a {
        text-align: center;
        padding: 15px 5px;
    }

    .sidebar a i {
        width: auto;
        font-size: 18px;
    }

    .main {
        margin-left: 70px;
    }

    .content {
        padding: 15px;
    }

    .topbar {
        padding: 15px;
    }

}

</style>

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">

    <div class="logo">
        <i class="fa-solid fa-school"></i>
        <span> ABSENSI SISWA</span>
    </div>


    <div class="menu-title">
        MENU UTAMA
    </div>

    <a href="dashboard.php" class="active">
        <i class="fa-solid fa-house"></i>
        <span class="menu-text">Dashboard</span>
    </a>

    <a href="siswa.php">
        <i class="fa-solid fa-user-graduate"></i>
        <span class="menu-text">Data Siswa</span>
    </a>

    <a href="kelas.php">
        <i class="fa-solid fa-school"></i>
        <span class="menu-text">Data Kelas</span>
    </a>

    <a href="presensi.php">
        <i class="fa-solid fa-clipboard-check"></i>
        <span class="menu-text">Input Presensi</span>
    </a>


    <div class="menu-title">
        PRESENSI
    </div>

    <a href="pengajuan.php">
        <i class="fa-solid fa-file-circle-plus"></i>
        <span class="menu-text">Pengajuan Izin</span>
    </a>

    <a href="verifikasi.php">
        <i class="fa-solid fa-check-double"></i>
        <span class="menu-text">Verifikasi Surat</span>
    </a>

    <a href="riwayat.php">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span class="menu-text">Riwayat Presensi</span>
    </a>


    <div class="menu-title">
        LAPORAN
    </div>

    <a href="rekap.php">
        <i class="fa-solid fa-chart-pie"></i>
        <span class="menu-text">Rekap Kehadiran</span>
    </a>

    <a href="laporan.php">
        <i class="fa-solid fa-file-pdf"></i>
        <span class="menu-text">Cetak Laporan</span>
    </a>


    <div class="menu-title">
        SISTEM
    </div>

    <a href="logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span class="menu-text">Logout</span>
    </a>

</div>


<!-- =========================
     MAIN
========================= -->

<div class="main">


<!-- TOPBAR -->

<div class="topbar">

    <div>

        <h5>
            Dashboard
        </h5>

        <small class="text-muted">
            Sistem Informasi Absensi Siswa
        </small>

    </div>


    <div class="user-info">

        <div class="user-icon">
            <i class="fa-solid fa-user"></i>
        </div>

        <div>

            <b>
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </b>

            <br>

            <small class="text-muted">
                <?= ucfirst($_SESSION['role']) ?>
            </small>

        </div>

    </div>

</div>


<!-- CONTENT -->

<div class="content">

    <div class="mb-4">

        <h3>
            Selamat Datang,
            <?= htmlspecialchars($_SESSION['nama']) ?> 👋
        </h3>

        <p class="text-muted">
            Ringkasan kehadiran siswa hari ini,
            <?= date('d-m-Y') ?>
        </p>

    </div>


<!-- =========================
     STATISTIK
========================= -->

<div class="row g-4">


    <!-- TOTAL SISWA -->

    <div class="col-lg-3 col-md-6">

        <div class="stat-card">

            <div class="icon icon-blue">
                <i class="fa-solid fa-user-graduate"></i>
            </div>

            <h6>Total Siswa</h6>

            <h2><?= $total_siswa ?></h2>

            <small class="text-muted">
                Siswa terdaftar
            </small>

        </div>

    </div>


    <!-- TOTAL KELAS -->

    <div class="col-lg-3 col-md-6">

        <div class="stat-card">

            <div class="icon icon-purple">
                <i class="fa-solid fa-school"></i>
            </div>

            <h6>Total Kelas</h6>

            <h2><?= $total_kelas ?></h2>

            <small class="text-muted">
                Kelas terdaftar
            </small>

        </div>

    </div>


    <!-- HADIR -->

    <div class="col-lg-3 col-md-6">

        <div class="stat-card">

            <div class="icon icon-green">
                <i class="fa-solid fa-check"></i>
            </div>

            <h6>Hadir Hari Ini</h6>

            <h2><?= $total_hadir ?></h2>

            <small class="text-muted">
                Data kehadiran hari ini
            </small>

        </div>

    </div>


    <!-- PENGAJUAN -->

    <div class="col-lg-3 col-md-6">

        <div class="stat-card">

            <div class="icon icon-yellow">
                <i class="fa-solid fa-bell"></i>
            </div>

            <h6>Menunggu Verifikasi</h6>

            <h2><?= $total_pengajuan ?></h2>

            <small class="text-muted">
                Pengajuan izin/sakit
            </small>

        </div>

    </div>

</div>


<!-- =========================
     STATUS PRESENSI
========================= -->

<div class="row g-4 mt-1">


    <div class="col-md-4">

        <div class="stat-card">

            <div class="icon icon-yellow">
                <i class="fa-solid fa-notes-medical"></i>
            </div>

            <h6>Sakit Hari Ini</h6>

            <h2><?= $total_sakit ?></h2>

        </div>

    </div>


    <div class="col-md-4">

        <div class="stat-card">

            <div class="icon icon-blue">
                <i class="fa-solid fa-file-circle-check"></i>
            </div>

            <h6>Izin Hari Ini</h6>

            <h2><?= $total_izin ?></h2>

        </div>

    </div>


    <div class="col-md-4">

        <div class="stat-card">

            <div class="icon icon-red">
                <i class="fa-solid fa-user-xmark"></i>
            </div>

            <h6>Alpa Hari Ini</h6>

            <h2><?= $total_alpa ?></h2>

        </div>

    </div>

</div>


<!-- =========================
     GRAFIK
========================= -->

<div class="row mt-4 g-4">

    <div class="col-lg-7">

        <div class="dashboard-card">

            <h5 class="mb-4">
                <i class="fa-solid fa-chart-column"></i>
                Statistik Kehadiran Hari Ini
            </h5>

            <canvas id="grafikPresensi"></canvas>

        </div>

    </div>


    <!-- PERSENTASE -->

    <div class="col-lg-5">

        <div class="dashboard-card">

            <h5>
                <i class="fa-solid fa-percent"></i>
                Ringkasan Kehadiran
            </h5>

            <hr>

            <div class="d-flex justify-content-between">

                <span>Persentase Hadir</span>

                <b>
                    <?= $persentase_hadir ?>%
                </b>

            </div>

            <div class="progress mt-2 mb-4">

                <div
                    class="progress-bar bg-success"
                    style="width: <?= $persentase_hadir ?>%">

                </div>

            </div>


            <div class="row text-center">

                <div class="col-3">

                    <h5 class="text-success">
                        <?= $total_hadir ?>
                    </h5>

                    <small>Hadir</small>

                </div>

                <div class="col-3">

                    <h5 class="text-warning">
                        <?= $total_sakit ?>
                    </h5>

                    <small>Sakit</small>

                </div>

                <div class="col-3">

                    <h5 class="text-primary">
                        <?= $total_izin ?>
                    </h5>

                    <small>Izin</small>

                </div>

                <div class="col-3">

                    <h5 class="text-danger">
                        <?= $total_alpa ?>
                    </h5>

                    <small>Alpa</small>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================
     TABEL PRESENSI TERBARU
========================= -->

<div class="dashboard-card mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h5>
            <i class="fa-solid fa-clock-rotate-left"></i>
            Presensi Terbaru
        </h5>

        <a href="riwayat.php"
           class="btn btn-primary btn-sm">

            Lihat Semua

        </a>

    </div>


    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Status</th>

                </tr>

            </thead>


            <tbody>

            <?php

            $no = 1;

            if (mysqli_num_rows($presensi_terbaru) > 0):

                while ($row = mysqli_fetch_assoc($presensi_terbaru)):

                    $badge = "";

                    if ($row['kode'] == "H") {
                        $badge = "badge-hadir";
                    }

                    elseif ($row['kode'] == "S") {
                        $badge = "badge-sakit";
                    }

                    elseif ($row['kode'] == "I") {
                        $badge = "badge-izin";
                    }

                    elseif ($row['kode'] == "A") {
                        $badge = "badge-alpa";
                    }

            ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td>
                        <?= htmlspecialchars($row['nis']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['nama']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                    </td>

                    <td>
                        <?= date(
                            'd-m-Y',
                            strtotime($row['tanggal'])
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['jam'] ?? '-') ?>
                    </td>

                    <td>

                        <span class="badge <?= $badge ?>">

                            <?= htmlspecialchars(
                                $row['status_presensi']
                            ) ?>

                        </span>

                    </td>

                </tr>

            <?php

                endwhile;

            else:

            ?>

                <tr>

                    <td colspan="7"
                        class="text-center text-muted">

                        Belum ada data presensi.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<!-- FOOTER -->

<div class="text-center text-muted mt-4 mb-3">

    <small>
        © <?= date('Y') ?>
        Sistem Informasi Absensi Siswa
    </small>

</div>


</div>
</div>


<!-- =========================
     CHART.JS
========================= -->

<script>

const ctx = document
    .getElementById('grafikPresensi');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: [
            'Hadir',
            'Sakit',
            'Izin',
            'Alpa'
        ],

        datasets: [{

            label: 'Jumlah Siswa',

            data: [
                <?= $total_hadir ?>,
                <?= $total_sakit ?>,
                <?= $total_izin ?>,
                <?= $total_alpa ?>
            ],

            backgroundColor: [
                '#22c55e',
                '#f59e0b',
                '#3b82f6',
                '#ef4444'
            ],

            borderRadius: 8

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {
                display: false
            }

        },

        scales: {

            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }

        }

    }

});

</script>

</body>
</html>