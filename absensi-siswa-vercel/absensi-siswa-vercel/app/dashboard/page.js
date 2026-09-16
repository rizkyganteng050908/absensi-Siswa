import { sql } from "../../lib/db";
import { requireUser } from "../../lib/auth";
import { redirect } from "next/navigation";
import { logoutAction } from "../actions/auth";

export default async function Dashboard() {
  const user = await requireUser(); if(!user) redirect("/login");
  const [s,k,p,t,h,pend] = await Promise.all([
    sql`SELECT COUNT(*)::int c FROM siswa`, sql`SELECT COUNT(*)::int c FROM kelas`,
    sql`SELECT COUNT(*)::int c FROM presensi`, sql`SELECT COUNT(*)::int c FROM presensi WHERE tanggal=CURRENT_DATE`,
    sql`SELECT COUNT(*)::int c FROM presensi p JOIN status_presensi st ON st.id=p.status_id WHERE p.tanggal=CURRENT_DATE AND st.kode='H'`,
    sql`SELECT COUNT(*)::int c FROM pengajuan_surat WHERE status='Menunggu'`
  ]);
  const pct=t[0].c?Math.round(h[0].c/t[0].c*100):0;
  return <Shell user={user} logout={logoutAction}><h2>Dashboard Statistik Kehadiran</h2>
    <div className="grid">
      <Stat n={s[0].c} t="Total Siswa" i="👨‍🎓"/><Stat n={k[0].c} t="Total Kelas" i="🏫"/>
      <Stat n={h[0].c} t="Hadir Hari Ini" i="✅"/><Stat n={pend[0].c} t="Surat Menunggu" i="✉️"/>
    </div>
    <div className="grid section"><div className="card" style={{gridColumn:"span 2"}}><h3>Kehadiran Hari Ini</h3><div className="num">{pct}%</div><div className="progress"><div style={{width:pct+"%"}}/></div><p className="muted">{h[0].c} hadir dari {t[0].c} presensi.</p></div>
    <div className="card" style={{gridColumn:"span 2"}}><h3>Ringkasan</h3><p>📌 Total data presensi: <b>{p[0].c}</b></p><p>📌 Surat perlu verifikasi: <b>{pend[0].c}</b></p><p>👤 Login sebagai: <b>{user.nama}</b></p></div></div>
  </Shell>;
}
function Stat({n,t,i}){return <div className="card stat"><div><div className="muted">{t}</div><div className="num">{n}</div></div><div className="icon">{i}</div></div>}
export function Shell({user,logout,children}){return <div className="layout"><aside className="sidebar"><div className="brand">📚 Absensi Siswa</div><nav className="nav">
<a href="/dashboard">🏠 Dashboard</a><a href="/siswa">👨‍🎓 Data Siswa</a><a href="/kelas">🏫 Data Kelas</a><a href="/presensi">📝 Input Presensi</a><a href="/riwayat">📋 Riwayat</a><a href="/surat">✉️ Surat Izin/Sakit</a><a href="/laporan">📊 Rekap & Laporan</a>
{user.role==="admin" && <a href="/setup">⚙️ Setup</a>}<form action={logout}><button className="btn red" style={{width:"100%",marginTop:10}}>🚪 Logout</button></form></nav></aside>
<main className="main"><header className="topbar"><span className="mobile">☰</span><b>Sistem Informasi Absensi Siswa</b><span className="user">👤 {user.nama} ({user.role})</span></header><div className="content">{children}</div></main></div>}
