<?php
require "config.php";
$title="Dashboard";
$totalSiswa=$conn->query("SELECT COUNT(*) c FROM siswa")->fetch_assoc()['c'];
$totalKelas=$conn->query("SELECT COUNT(*) c FROM kelas")->fetch_assoc()['c'];
$today=date('Y-m-d');
$hadir=$conn->query("SELECT COUNT(*) c FROM presensi p JOIN status_presensi s ON s.id=p.status_id WHERE p.tanggal='$today' AND s.kode='H'")->fetch_assoc()['c'];
$totalToday=$conn->query("SELECT COUNT(*) c FROM presensi WHERE tanggal='$today'")->fetch_assoc()['c'];
$persen=$totalToday?round($hadir/$totalToday*100):0;
$pending=$conn->query("SELECT COUNT(*) c FROM pengajuan_surat WHERE status='Menunggu'")->fetch_assoc()['c'];
include "header.php";
?>
<h2>Dashboard Statistik Kehadiran</h2><p class="muted">Ringkasan data hari <?=date('d-m-Y')?></p>
<div class="grid">
<div class="card stat"><div><div class="muted">Total Siswa</div><div class="num"><?=e($totalSiswa)?></div></div><div class="icon">👨‍🎓</div></div>
<div class="card stat"><div><div class="muted">Total Kelas</div><div class="num"><?=e($totalKelas)?></div></div><div class="icon">🏫</div></div>
<div class="card stat"><div><div class="muted">Hadir Hari Ini</div><div class="num"><?=e($hadir)?></div></div><div class="icon">✅</div></div>
<div class="card stat"><div><div class="muted">Pengajuan Menunggu</div><div class="num"><?=e($pending)?></div></div><div class="icon">✉️</div></div>
</div>
<div class="grid section">
<div class="card" style="grid-column:span 2"><h3>Persentase Presensi Hari Ini</h3><div style="font-size:42px;font-weight:800"><?=e($persen)?>%</div><div class="progress"><div style="width:<?=$persen?>%"></div></div><p class="muted">Hadir <?=e($hadir)?> dari <?=e($totalToday)?> data presensi.</p></div>
<div class="card" style="grid-column:span 2"><h3>Notifikasi / Ringkasan</h3>
<p>📌 Data siswa: <b><?=e($totalSiswa)?></b></p><p>📌 Presensi hari ini: <b><?=e($totalToday)?></b></p>
<p>📌 Surat menunggu verifikasi: <b><?=e($pending)?></b></p>
<?php if($_SESSION['user']['role']==='admin'): ?><a class="btn" href="verifikasi.php">Verifikasi Surat</a><?php endif; ?>
</div></div>
<?php include "footer.php"; ?>
