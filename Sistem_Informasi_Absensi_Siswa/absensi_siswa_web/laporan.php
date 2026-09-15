<?php
require "config.php"; login_required(); $title="Rekapitulasi & Laporan";
$dari=$_GET['dari']??date('Y-m-01');$sampai=$_GET['sampai']??date('Y-m-d');$kelas_id=(int)($_GET['kelas_id']??0);
$where="p.tanggal BETWEEN '".$conn->real_escape_string($dari)."' AND '".$conn->real_escape_string($sampai)."'";if($kelas_id)$where.=" AND s.kelas_id=$kelas_id";
$rows=$conn->query("SELECT s.nis,s.nama,k.nama_kelas,
SUM(sp.kode='H') hadir,SUM(sp.kode='S') sakit,SUM(sp.kode='I') izin,SUM(sp.kode='A') alpa,COUNT(p.id) total
FROM siswa s JOIN kelas k ON k.id=s.kelas_id LEFT JOIN presensi p ON p.siswa_id=s.id AND $where
LEFT JOIN status_presensi sp ON sp.id=p.status_id GROUP BY s.id ORDER BY k.nama_kelas,s.nama");
$kelas=$conn->query("SELECT * FROM kelas ORDER BY nama_kelas");
if(isset($_GET['export']) && $_GET['export']==='excel'){
 header('Content-Type:text/csv; charset=utf-8');header('Content-Disposition:attachment; filename=rekap_presensi.csv');$out=fopen('php://output','w');fputcsv($out,['NIS','Nama','Kelas','Hadir','Sakit','Izin','Alpa','Total','Persentase']);
 while($r=$rows->fetch_assoc()){ $pct=$r['total']?round($r['hadir']/$r['total']*100,2):0;fputcsv($out,[$r['nis'],$r['nama'],$r['nama_kelas'],$r['hadir'],$r['sakit'],$r['izin'],$r['alpa'],$r['total'],$pct.'%']); }fclose($out);exit;
}
include "header.php";?>
<div class="actions"><h2 style="margin-right:auto">Rekapitulasi Persentase Kehadiran</h2><button class="btn no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button></div>
<div class="card no-print"><form class="filters"><div><label>Dari</label><input type="date" name="dari" value="<?=e($dari)?>"></div><div><label>Sampai</label><input type="date" name="sampai" value="<?=e($sampai)?>"></div><div><label>Kelas</label><select name="kelas_id"><option value="0">Semua kelas</option><?php while($k=$kelas->fetch_assoc()):?><option value="<?=$k['id']?>" <?=$kelas_id==$k['id']?'selected':''?>><?=e($k['nama_kelas'])?></option><?php endwhile;?></select></div><div class="small"><button class="btn">Tampilkan</button></div><div class="small"><a class="btn green" href="?dari=<?=e($dari)?>&sampai=<?=e($sampai)?>&kelas_id=<?=$kelas_id?>&export=excel">⬇ Excel/CSV</a></div></form></div>
<div class="card section"><div class="print-only"><h1>Laporan Rekapitulasi Presensi Siswa</h1><p>Periode: <?=e($dari)?> s/d <?=e($sampai)?></p></div><div class="table-wrap"><table><tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Hadir</th><th>Sakit</th><th>Izin</th><th>Alpa</th><th>Total</th><th>% Hadir</th></tr><?php $no=1;while($r=$rows->fetch_assoc()):$pct=$r['total']?round($r['hadir']/$r['total']*100,2):0;?><tr><td><?=$no++?></td><td><?=e($r['nis'])?></td><td><?=e($r['nama'])?></td><td><?=e($r['nama_kelas'])?></td><td><?=$r['hadir']?></td><td><?=$r['sakit']?></td><td><?=$r['izin']?></td><td><?=$r['alpa']?></td><td><?=$r['total']?></td><td><b><?=$pct?>%</b></td></tr><?php endwhile;?></table></div></div>
<?php include "footer.php"; ?>
