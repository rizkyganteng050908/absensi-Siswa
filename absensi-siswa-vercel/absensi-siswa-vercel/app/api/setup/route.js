import { setupDatabase } from "../../../lib/schema";
export async function GET(req) {
  const url = new URL(req.url);
  if (url.searchParams.get("key") !== process.env.SETUP_SECRET) return Response.json({error:"Unauthorized"},{status:401});
  await setupDatabase();
  return Response.json({ok:true,message:"Database siap. Login admin: admin / admin123"});
}
