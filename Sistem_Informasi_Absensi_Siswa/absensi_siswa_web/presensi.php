<?php
require "config.php"; login_required(); $title="Input Presensi";
$tanggal=$_POST['tanggal']??$_GET['tanggal']??date('Y-m-d');$kelas_id=(int)($_POST['kelas_id']??$_GET['kelas_id']??0);$jam=$_POST['jam_pelajaran']??'Harian';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan_presensi'])){
 $s=$conn->prepare("INSERT INTO presensi(siswa_id,tanggal,jam_pelajaran,status_id,keterangan,created_by) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status_id=VALUES(status_id),keterangan=VALUES(keterangan),created_by=VALUES(created_by)");
 foreach($_POST['status'] as $sid=>$status){$sid=(int)$sid;$status=(int)$status;$ket=trim($_POST['keterangan'][$sid]??'');$uid=$_SESSION['user']['id'];$s->bind_param("isisss",$sid,$tanggal,$jam,$status,$ket,$uid);$s->execute();}
 flash('success','Presensi berhasil disimpan ke database.');header("Location:presensi.php?tanggal=".urlencode($tanggal)."&kelas_id=".$kelas_id."&jam=".urlencode($jam));exit;
}
$kelas=$conn->query("SELECT * FROM kelas ORDER BY nama_kelas");$statuses=$conn->query("SELECT * FROM status_presensi ORDER BY id");
$st=[];while($x=$statuses->fetch_assoc())$st[]=$x;
$students=$kelas_id?$conn->query("SELECT * FROM siswa WHERE kelas_id=$kelas_id ORDER BY nama"):false;
$old=[];if($students){$q=$conn->query("SELECT * FROM presensi WHERE tanggal='".$conn->real_escape_string($tanggal)."' AND jam_pelajaran='".$conn->real_escape_string($jam)."'");while($x=$q->fetch_assoc())$old[$x['siswa_id']]=$x;}
include "header.php";?>
<h2>Input Presensi Harian / Jam Pelajaran</h2>
<div class="card"><form method="get" class="filters"><div><label>Tanggal</label><input type="date" name="tanggal" value="<?=e($tanggal)?>"></div><div><label>Kelas</label><select name="kelas_id" required><option value="">Pilih kelas</option><?php while($k=$kelas->fetch_assoc()):?><option value="<?=$k['id']?>" <?=$kelas_id==$k['id']?'selected':''?>><?=e($k['nama_kelas'])?></option><?php endwhile;?></select></div><div><label>Jam Pelajaran</label><input name="jam" value="<?=e($jam)?>" placeholder="Contoh: 1-2 / Harian"></div><div class="small"><button class="btn">Tampilkan</button></div></form></div>
<?php if($students): ?><form method="post" class="section"><input type="hidden" name="tanggal" value="<?=e($tanggal)?>"><input type="hidden" name="kelas_id" value="<?=$kelas_id?>"><input type="hidden" name="jam_pelajaran" value="<?=e($jam)?>">
<div class="card table-wrap"><table><tr><th>No</th><th>NIS</th><th>Nama</th><th>Status</th><th>Keterangan</th></tr><?php $no=1;while($s=$students->fetch_assoc()):$cur=$old[$s['id']]['status_id']??1;?><tr><td><?=$no++?></td><td><?=e($s['nis'])?></td><td><?=e($s['nama'])?></td><td><select name="status[<?=$s['id']?>]"><?php foreach($st as $x):?><option value="<?=$x['id']?>" <?=$cur==$x['id']?'selected':''?>><?=e($x['nama_status'])?></option><?php endforeach;?></select></td><td><input name="keterangan[<?=$s['id']?>]" value="<?=e($old[$s['id']]['keterangan']??'')?>"></td></tr><?php endwhile;?></table></div><br><button class="btn green" name="simpan_presensi">💾 Simpan Presensi</button></form><?php endif;?>
<?php include "footer.php"; ?>
