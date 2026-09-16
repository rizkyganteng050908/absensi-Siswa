 "use server";
import { redirect } from "next/navigation";
import { loginUser, logoutUser } from "../../lib/auth";

export async function loginAction(formData) {
  const ok = await loginUser(String(formData.get("username") || ""), String(formData.get("password") || ""));
  if (!ok) redirect("/login?error=1");
  redirect("/dashboard");
}
export async function logoutAction() {
  await logoutUser();
  redirect("/login");
}
