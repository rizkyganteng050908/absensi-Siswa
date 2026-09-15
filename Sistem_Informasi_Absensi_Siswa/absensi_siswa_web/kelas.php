<?php
require "config.php"; admin_required(); $title="Kelola Data Kelas";
if(isset($_GET['hapus'])){$id=(int)$_GET['hapus']; if($conn->query("DELETE FROM kelas WHERE id=$id")) flash('success','Kelas dihapus.'); else flash('danger','Kelas tidak dapat dihapus karena masih memiliki siswa.'); header("Location: kelas.php");exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=(int)($_POST['id']??0);$nama=trim($_POST['nama_kelas']);$wali=trim($_POST['wali_kelas']);
 if($id){$s=$conn->prepare("UPDATE kelas SET nama_kelas=?,wali_kelas=? WHERE id=?");$s->bind_param("ssi",$nama,$wali,$id);}
 else{$s=$conn->prepare("INSERT INTO kelas(nama_kelas,wali_kelas) VALUES(?,?)");$s->bind_param("ss",$nama,$wali);}
 $s->execute();flash('success','Data kelas disimpan.');header("Location: kelas.php");exit;
}
$edit=null;if(isset($_GET['edit'])){$id=(int)$_GET['edit'];$edit=$conn->query("SELECT * FROM kelas WHERE id=$id")->fetch_assoc();}
$rows=$conn->query("SELECT k.*,COUNT(s.id) jumlah FROM kelas k LEFT JOIN siswa s ON s.kelas_id=k.id GROUP BY k.id ORDER BY k.nama_kelas");
include "header.php"; ?>
<div class="actions"><h2 style="margin-right:auto">Data Kelas</h2></div>
<div class="card"><h3><?= $edit?'Edit':'Tambah' ?> Kelas</h3><form method="post"><input type="hidden" name="id" value="<?=e($edit['id']??0)?>">
<div class="form-grid"><div><label>Nama Kelas</label><input name="nama_kelas" required value="<?=e($edit['nama_kelas']??'')?>"></div><div><label>Wali Kelas</label><input name="wali_kelas" value="<?=e($edit['wali_kelas']??'')?>"></div></div><br><button class="btn">Simpan</button></form></div>
<div class="card section table-wrap"><table><tr><th>No</th><th>Kelas</th><th>Wali Kelas</th><th>Jumlah Siswa</th><th>Aksi</th></tr><?php $no=1;while($r=$rows->fetch_assoc()):?><tr><td><?=$no++?></td><td><?=e($r['nama_kelas'])?></td><td><?=e($r['wali_kelas'])?></td><td><?=$r['jumlah']?></td><td><a class="btn gray" href="?edit=<?=$r['id']?>">Edit</a> <a class="btn red" onclick="return confirm('Hapus kelas?')" href="?hapus=<?=$r['id']?>">Hapus</a></td></tr><?php endwhile;?></table></div>
<?php include "footer.php"; ?>
