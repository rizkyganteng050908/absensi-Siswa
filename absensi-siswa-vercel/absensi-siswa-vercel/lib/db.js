import { neon } from "@neondatabase/serverless";

export const sql = neon(process.env.DATABASE_URL);

export async function query(strings, ...values) {
  return sql(strings, ...values);
}
