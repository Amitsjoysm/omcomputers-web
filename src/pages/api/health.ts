import type { APIRoute } from 'astro';
import { env } from '../../lib/env';
import { db } from '../../lib/db';

// Diagnostic endpoint: GET /api/health
// Shows whether the app received its environment variables and whether it
// can connect to MySQL (and creates the tables on the first successful
// connection). No secret VALUES are exposed — only whether each variable is
// set, plus the non-sensitive DB host/port it is trying to reach, and the
// error CODE if the connection fails.
//
// Safe to leave in place, but you can delete this file once the site is
// working if you prefer not to expose deployment status.

function hintFor(code?: string): string {
  switch (code) {
    case 'ECONNREFUSED':
      return 'Nothing is listening at DB_HOST:DB_PORT. On Hostinger container/web-app hosting the database is a separate service — DB_HOST is usually NOT "localhost". Use the exact host shown in hPanel → Databases → your MySQL DB (and try DB_SOCKET if hPanel gives a socket path).';
    case 'ER_ACCESS_DENIED_ERROR':
      return 'Wrong DB_USER / DB_PASSWORD, or that user is not attached to DB_NAME. Re-copy the password (no stray spaces) and confirm the user is added to the database in hPanel.';
    case 'ER_BAD_DB_ERROR':
      return 'DB_NAME does not exist — copy the exact database name from hPanel.';
    case 'ETIMEDOUT':
      return 'Connection timed out — DB_HOST/DB_PORT is likely wrong or the database is not reachable from the app.';
    case 'ENOTFOUND':
      return 'DB_HOST could not be resolved — use the exact host string from hPanel.';
    case undefined:
    case null:
      return '';
    default:
      return 'Check the DB_* environment variables against hPanel → Databases.';
  }
}

export const GET: APIRoute = async () => {
  const envStatus = {
    // presence only (never the value) for secrets
    DB_PASSWORD_set: !!env('DB_PASSWORD'),
    DB_USER_set: !!env('DB_USER'),
    DB_NAME_set: !!env('DB_NAME'),
    ADMIN_PASSWORD_set: !!env('ADMIN_PASSWORD'),
    ADMIN_SECRET_set: !!env('ADMIN_SECRET'),
    // non-sensitive connection target, shown so host/port mistakes are visible
    DB_HOST: env('DB_HOST') || '(unset → defaults to 127.0.0.1)',
    DB_PORT: env('DB_PORT') || '(unset → defaults to 3306)',
    DB_SOCKET: env('DB_SOCKET') || '(unset → using TCP host/port)',
    NODE_ENV: env('NODE_ENV') || '(unset)',
  };

  const database: {
    connected: boolean;
    tablesReady: boolean;
    tableCount: number;
    error: string | null;
    hint: string;
  } = { connected: false, tablesReady: false, tableCount: 0, error: null, hint: '' };

  try {
    const pool = await db(); // creates + seeds tables on first success
    const [rows] = await pool.query<any[]>('SHOW TABLES');
    database.connected = true;
    database.tableCount = Array.isArray(rows) ? rows.length : 0;
    database.tablesReady = database.tableCount > 0;
  } catch (e: any) {
    database.error = e?.code || e?.message || 'unknown error';
    database.hint = hintFor(e?.code);
  }

  const ok = database.connected;
  return new Response(JSON.stringify({ ok, env: envStatus, database }, null, 2), {
    status: ok ? 200 : 503,
    headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-store' },
  });
};
