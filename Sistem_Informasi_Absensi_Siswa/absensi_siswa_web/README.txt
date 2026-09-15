SISTEM INFORMASI ABSENSI SISWA - PHP NATIVE + MYSQL

CARA INSTALL:
1. Pastikan XAMPP/WAMP/Laragon dan MySQL aktif.
2. Salin folder 'absensi_siswa_web' ke htdocs (XAMPP) atau folder web server.
3. Buka http://localhost/absensi_siswa_web/setup.php
4. Database db_absensi_siswa akan dibuat otomatis.
5. Login: admin / admin123
6. Setelah berhasil, hapus/rename setup.php.

FITUR:
- Login/logout
- Dashboard statistik
- Kelola siswa & kelas
- Master status Hadir/Sakit/Izin/Alpa
- Input presensi harian/jam pelajaran
- Riwayat presensi
- Pengajuan & verifikasi surat
- Rekap persentase
- Cetak / Simpan PDF melalui dialog Print browser
- Export CSV yang dapat dibuka di Excel

CATATAN:
- Form pengajuan surat pada versi starter belum mewajibkan upload file fisik; database sudah menyiapkan kolom file_surat untuk pengembangan upload dokumen.
- Untuk produksi, tambahkan CSRF, pembatasan hak akses yang lebih detail, upload validation, dan HTTPS.
