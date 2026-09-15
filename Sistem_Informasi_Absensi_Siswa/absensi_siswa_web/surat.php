<?php
require "config.php"; login_required(); $title="Pengajuan Surat Izin / Sakit";
if($_SERVER['REQUEST_METHOD']==='POST'){
$siswa=(int)$_POST['siswa_id'];$mulai=$_POST['tanggal_mulai'];$selesai=$_POST['tanggal_selesai'];$jenis=$_POST['jenis'];$alasan=trim($_POST['alasan']);
$stmt=$conn->prepare("INSERT INTO pengajuan_surat(siswa_id,tanggal_mulai,tanggal_selesai,jenis,alasan) VALUES(?,?,?,?,?)");$stmt->bind_param("issss",$siswa,$mulai,$selesai,$jenis,$alasan);$stmt->execute();flash('success','Pengajuan berhasil dibuat dan menunggu verifikasi.');header("Location:surat.php");exit;
}
$students=$conn->query("SELECT id,nis,nama FROM siswa ORDER BY nama");$mine=$conn->query("SELECT p.*,s.nama,s.nis FROM pengajuan_surat p JOIN siswa s ON s.id=p.siswa_id ORDER BY p.diajukan_at DESC");
include "header.php";?>
<h2>Pengajuan Surat Izin / Sakit</h2><p class="muted">Form ini dapat digunakan untuk mencatat pengajuan. Verifikasi dilakukan Admin.</p>
<div class="card"><form method="post"><div class="form-grid"><div><label>Siswa</label><select name="siswa_id" required><option value="">Pilih siswa</option><?php while($s=$students->fetch_assoc()):?><option value="<?=$s['id']?>"><?=e($s['nis'].' - '.$s['nama'])?></option><?php endwhile;?></select></div><div><label>Jenis</label><select name="jenis"><option>Sakit</option><option>Izin</option></select></div><div><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required></div><div><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required></div></div><label>Alasan</label><textarea name="alasan" required></textarea><br><button class="btn">Ajukan Surat</button></form></div>
<div class="card section table-wrap"><table><tr><th>Siswa</th><th>Jenis</th><th>Periode</th><th>Alasan</th><th>Status</th></tr><?php while($r=$mine->fetch_assoc()):?><tr><td><?=e($r['nis'].' - '.$r['nama'])?></td><td><?=e($r['jenis'])?></td><td><?=e($r['tanggal_mulai'].' s/d '.$r['tanggal_selesai'])?></td><td><?=e($r['alasan'])?></td><td><?=e($r['status'])?></td></tr><?php endwhile;?></table></div>
<?php include "footer.php"; ?>
