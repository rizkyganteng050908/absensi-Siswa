import { cookies } from "next/headers";
import bcrypt from "bcryptjs";
import { sql } from "./db";

const COOKIE = "absensi_user";

export async function getUser() {
  const jar = await cookies();
  const id = jar.get(COOKIE)?.value;
  if (!id) return null;
  const rows = await sql`SELECT id, nama, username, role FROM users WHERE id=${Number(id)} LIMIT 1`;
  return rows[0] || null;
}

export async function requireUser() {
  const user = await getUser();
  if (!user) return null;
  return user;
}

export async function requireAdmin() {
  const user = await getUser();
  return user?.role === "admin" ? user : null;
}

export async function loginUser(username, password) {
  const rows = await sql`SELECT * FROM users WHERE username=${username} LIMIT 1`;
  const user = rows[0];
  if (!user || !(await bcrypt.compare(password, user.password))) return false;
  const jar = await cookies();
  jar.set(COOKIE, String(user.id), {
    httpOnly: true, sameSite: "lax", secure: process.env.NODE_ENV === "production",
    path: "/", maxAge: 60 * 60 * 24 * 7
  });
  return true;
}

export async function logoutUser() {
  const jar = await cookies();
  jar.delete(COOKIE);
}
