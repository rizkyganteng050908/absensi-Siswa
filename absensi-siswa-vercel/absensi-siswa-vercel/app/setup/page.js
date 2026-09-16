import { setupDatabase } from "../../lib/schema";
import { redirect } from "next/navigation";

export default function Setup() {
  async function action(formData) {
    "use server";
    if (String(formData.get("secret")) !== process.env.SETUP_SECRET) throw new Error("Setup secret salah");
    await setupDatabase();
    redirect("/login?setup=success");
  }
  return <main className="login"><div className="loginbox">
    <h1>⚙️ Setup Database</h1>
    <p className="muted">Hubungkan Neon dulu di Vercel, lalu jalankan setup satu kali.</p>
    <form action={action}><label>Setup Secret</label><input type="password" name="secret" required />
    <button className="btn" style={{width:"100%",marginTop:14}}>Buat Database</button></form>
  </div></main>;
}
