import bcrypt from "bcryptjs";
import { sql } from "./db";

export async function setupDatabase() {
  await sql`CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY, nama VARCHAR(100) NOT NULL, username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, role VARCHAR(20) NOT NULL DEFAULT 'guru', created_at TIMESTAMP DEFAULT NOW()
  )`;
  await sql`CREATE TABLE IF NOT EXISTS kelas (
    id SERIAL PRIMARY KEY, nama_kelas VARCHAR(50) NOT NULL, wali_kelas VARCHAR(100)
  )`;
  await sql`CREATE TABLE IF NOT EXISTS siswa (
    id SERIAL PRIMARY KEY, nis VARCHAR(30) UNIQUE NOT NULL, nama VARCHAR(100) NOT NULL,
    jk VARCHAR(1) NOT NULL, kelas_id INTEGER NOT NULL REFERENCES kelas(id) ON DELETE RESTRICT
  )`;
  await sql`CREATE TABLE IF NOT EXISTS status_presensi (
    id SERIAL PRIMARY KEY, kode VARCHAR(10) UNIQUE NOT NULL, nama_status VARCHAR(30) NOT NULL, warna VARCHAR(20)
  )`;
  await sql`CREATE TABLE IF NOT EXISTS presensi (
    id SERIAL PRIMARY KEY, siswa_id INTEGER NOT NULL REFERENCES siswa(id) ON DELETE CASCADE,
    tanggal DATE NOT NULL, jam_pelajaran VARCHAR(50) NOT NULL DEFAULT 'Harian',
    status_id INTEGER NOT NULL REFERENCES status_presensi(id), keterangan VARCHAR(255),
    created_by INTEGER REFERENCES users(id) ON DELETE SET NULL, created_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(siswa_id, tanggal, jam_pelajaran)
  )`;
  await sql`CREATE TABLE IF NOT EXISTS pengajuan_surat (
    id SERIAL PRIMARY KEY, siswa_id INTEGER NOT NULL REFERENCES siswa(id) ON DELETE CASCADE,
    tanggal_mulai DATE NOT NULL, tanggal_selesai DATE NOT NULL, jenis VARCHAR(20) NOT NULL,
    alasan TEXT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'Menunggu',
    catatan_verifikasi TEXT, diajukan_at TIMESTAMP DEFAULT NOW(), diverifikasi_at TIMESTAMP,
    diverifikasi_oleh INTEGER REFERENCES users(id) ON DELETE SET NULL
  )`;

  for (const s of [
    ["H","Hadir","#16a34a"], ["S","Sakit","#f59e0b"], ["I","Izin","#2563eb"], ["A","Alpa","#dc2626"]
  ]) {
    await sql`INSERT INTO status_presensi(kode,nama_status,warna)
      VALUES(${s[0]},${s[1]},${s[2]}) ON CONFLICT(kode) DO NOTHING`;
  }

  const existing = await sql`SELECT COUNT(*)::int AS c FROM users`;
  if (existing[0].c === 0) {
    const hash = await bcrypt.hash("admin123", 10);
    await sql`INSERT INTO users(nama,username,password,role) VALUES('Administrator','admin',${hash},'admin')`;
  }
  const kc = await sql`SELECT COUNT(*)::int AS c FROM kelas`;
  if (kc[0].c === 0) {
    await sql`INSERT INTO kelas(nama_kelas,wali_kelas) VALUES
      ('X RPL 1','Wali Kelas X RPL 1'),
      ('XI RPL 1','Wali Kelas XI RPL 1'),
      ('XII RPL 1','Wali Kelas XII RPL 1')`;
  }
}
