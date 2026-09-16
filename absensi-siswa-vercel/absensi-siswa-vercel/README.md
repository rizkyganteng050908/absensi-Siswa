# Absensi Siswa — Vercel + Neon

Versi ini mengubah aplikasi PHP + MySQL menjadi **Next.js + Neon Postgres**, sehingga cocok untuk deploy di Vercel. Vercel menyediakan integrasi Neon Postgres; koneksi database dapat diberikan melalui `DATABASE_URL`. citeturn0search0

## Fitur
- Login Admin
- Dashboard statistik
- Kelola siswa
- Kelola kelas
- Master status Hadir/Sakit/Izin/Alpa
- Input presensi harian/jam pelajaran
- Riwayat presensi
- Pengajuan & verifikasi surat
- Rekap persentase
- Cetak melalui Print browser

## Deploy paling mudah
1. Push isi folder ini ke GitHub.
2. Import repository di Vercel.
3. Root Directory: `/` (jangan pilih folder PHP lama).
4. Framework Preset: Next.js.
5. Deploy.
6. Di Vercel buka **Storage / Marketplace → Neon** dan buat/connect database. Vercel mendokumentasikan Neon sebagai integrasi Postgres untuk aplikasi Next.js. citeturn0search0turn0search1
7. Tambahkan Environment Variable:
   - `SETUP_SECRET` = kata rahasia buatanmu.
   - `DATABASE_URL` biasanya otomatis tersedia setelah Neon terhubung. citeturn0search0
8. Setelah redeploy, buka:
   `https://NAMA-PROJECT.vercel.app/setup`
9. Masukkan `SETUP_SECRET`.
10. Database/tabel dan akun awal dibuat otomatis.
11. Login:
   - username: `admin`
   - password: `admin123`
12. Segera ganti password admin dengan fitur pengembangan berikutnya / langsung ubah di database.

## Catatan
- Database versi Vercel ini memakai PostgreSQL/Neon, bukan MySQL. Neon tersedia sebagai integrasi Vercel dan memiliki paket mulai $0 menurut halaman Marketplace saat dokumentasi ini dibuat. citeturn0search0
- Untuk production, tambahkan upload file surat, CSRF, rate limiting, dan halaman ganti password.
