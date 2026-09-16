import { loginAction } from "../actions/auth";

export default async function Login({ searchParams }) {
  const sp = await searchParams;
  return <main className="login"><div className="loginbox">
    <h1>📚 Absensi Siswa</h1><p className="muted">Sistem Informasi Absensi Siswa berbasis Vercel</p>
    {sp?.error && <div className="alert danger">Username atau password salah.</div>}
    <form action={loginAction}>
      <label>Username</label><input name="username" required />
      <label>Password</label><input type="password" name="password" required />
      <button className="btn" style={{width:"100%",marginTop:14}}>Login</button>
    </form>
    <div className="card" style={{marginTop:16}}>Demo: <b>admin</b> / <b>admin123</b></div>
    <p className="muted" style={{fontSize:12}}>Ganti password admin setelah instalasi.</p>
  </div></main>;
}
