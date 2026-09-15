<?php login_required(); ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title ?? 'Absensi Siswa')?></title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="layout">
<aside class="sidebar"><div class="brand">📚 <span>Absensi Siswa</span></div>
<nav>
<a href="index.php">🏠 Dashboard</a>
<a href="siswa.php">👨‍🎓 Data Siswa</a>
<a href="kelas.php">🏫 Data Kelas</a>
<a href="presensi.php">📝 Input Presensi</a>
<a href="riwayat.php">📋 Riwayat Presensi</a>
<a href="surat.php">✉️ Pengajuan Surat</a>
<?php if($_SESSION['user']['role']==='admin'): ?><a href="verifikasi.php">✅ Verifikasi Surat</a><?php endif; ?>
<a href="laporan.php">📊 Rekap & Laporan</a>
<?php if($_SESSION['user']['role']==='admin'): ?><a href="master_status.php">⚙️ Master Status</a><?php endif; ?>
<a href="logout.php">🚪 Logout</a>
</nav></aside>
<main class="main"><header class="topbar"><button class="menu" onclick="document.querySelector('.sidebar').classList.toggle('show')">☰</button>
<div><b><?=e($title ?? 'Dashboard')?></b></div><div class="user">👤 <?=e($_SESSION['user']['nama'])?> (<?=e($_SESSION['user']['role'])?>)</div></header>
<div class="content"><?php show_flash(); ?>
