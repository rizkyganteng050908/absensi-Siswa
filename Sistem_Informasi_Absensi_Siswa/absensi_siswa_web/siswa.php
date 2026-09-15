<?php
require "config.php"; admin_required(); $title="Kelola Data Siswa";
if(isset($_GET['hapus'])){$id=(int)$_GET['hapus'];$conn->query("DELETE FROM siswa WHERE id=$id");flash('success','Siswa dihapus.');header("Location:siswa.php");exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
$id=(int)($_POST['id']??0);$nis=trim($_POST['nis']);$nama=trim($_POST['nama']);$jk=$_POST['jk'];$kelas=(int)$_POST['kelas_id'];
if($id){$s=$conn->prepare("UPDATE siswa SET nis=?,nama=?,jk=?,kelas_id=? WHERE id=?");$s->bind_param("sssii",$nis,$nama,$jk,$kelas,$id);}
else{$s=$conn->prepare("INSERT INTO siswa(nis,nama,jk,kelas_id) VALUES(?,?,?,?)");$s->bind_param("sssi",$nis,$nama,$jk,$kelas);}
if(!$s->execute()) flash('danger','Gagal menyimpan. NIS mungkin sudah digunakan.'); else flash('success','Data siswa disimpan.');
header("Location:siswa.php");exit;}
$edit=null;if(isset($_GET['edit'])){$id=(int)$_GET['edit'];$edit=$conn->query("SELECT * FROM siswa WHERE id=$id")->fetch_assoc();}
$kelas=$conn->query("SELECT * FROM kelas ORDER BY nama_kelas");$rows=$conn->query("SELECT s.*,k.nama_kelas FROM siswa s JOIN kelas k ON k.id=s.kelas_id ORDER BY k.nama_kelas,s.nama");
include "header.php"; ?>
<h2>Data Siswa</h2><div class="card"><h3><?= $edit?'Edit':'Tambah' ?> Siswa</h3><form method="post"><input type="hidden" name="id" value="<?=e($edit['id']??0)?>">
<div class="form-grid"><div><label>NIS</label><input name="nis" required value="<?=e($edit['nis']??'')?>"></div><div><label>Nama Siswa</label><input name="nama" required value="<?=e($edit['nama']??'')?>"></div>
<div><label>Jenis Kelamin</label><select name="jk"><option value="L" <?=($edit['jk']??'')==='L'?'selected':''?>>Laki-laki</option><option value="P" <?=($edit['jk']??'')==='P'?'selected':''?>>Perempuan</option></select></div>
<div><label>Kelas</label><select name="kelas_id" required><?php while($k=$kelas->fetch_assoc()):?><option value="<?=$k['id']?>" <?=($edit['kelas_id']??0)==$k['id']?'selected':''?>><?=e($k['nama_kelas'])?></option><?php endwhile;?></select></div></div><br><button class="btn">Simpan</button></form></div>
<div class="card section table-wrap"><table><tr><th>No</th><th>NIS</th><th>Nama</th><th>L/P</th><th>Kelas</th><th>Aksi</th></tr><?php $no=1;while($r=$rows->fetch_assoc()):?><tr><td><?=$no++?></td><td><?=e($r['nis'])?></td><td><?=e($r['nama'])?></td><td><?=e($r['jk'])?></td><td><?=e($r['nama_kelas'])?></td><td><a class="btn gray" href="?edit=<?=$r['id']?>">Edit</a> <a class="btn red" onclick="return confirm('Hapus siswa?')" href="?hapus=<?=$r['id']?>">Hapus</a></td></tr><?php endwhile;?></table></div>
<?php include "footer.php"; ?>
