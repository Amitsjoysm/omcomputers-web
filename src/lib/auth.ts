import { createHash, createHmac, timingSafeEqual } from 'node:crypto';
import { env } from './env';

export const SESSION_COOKIE = 'oms_admin';
const SESSION_TTL_MS = 7 * 24 * 60 * 60 * 1000; // 7 days

function secret(): string {
  const s = env('ADMIN_SECRET');
  if (!s) throw new Error('ADMIN_SECRET environment variable is not set');
  return s;
}

function sha256(input: string): Buffer {
  return createHash('sha256').update(input, 'utf8').digest();
}

/** Constant-time password check against the ADMIN_PASSWORD env var. */
export function checkPassword(input: string): boolean {
  const expected = env('ADMIN_PASSWORD');
  if (!expected || !input) return false;
  return timingSafeEqual(sha256(input), sha256(expected));
}

function sign(payload: string): string {
  return createHmac('sha256', secret()).update(payload).digest('hex');
}

/** Stateless session token: "<expiryMs>.<hmac>" */
export function createSessionToken(): string {
  const exp = String(Date.now() + SESSION_TTL_MS);
  return `${exp}.${sign(exp)}`;
}

export function verifySessionToken(token: string | undefined): boolean {
  if (!token) return false;
  const dot = token.indexOf('.');
  if (dot === -1) return false;
  const exp = token.slice(0, dot);
  const sig = token.slice(dot + 1);
  if (!/^\d+$/.test(exp) || Number(exp) < Date.now()) return false;
  const expected = sign(exp);
  if (sig.length !== expected.length) return false;
  try {
    return timingSafeEqual(Buffer.from(sig, 'utf8'), Buffer.from(expected, 'utf8'));
  } catch {
    return false;
  }
}

// ── Login rate limiting (in-memory, per IP) ──────────────────────────
const WINDOW_MS = 15 * 60 * 1000;
const MAX_ATTEMPTS = 8;
const attempts = new Map<string, { count: number; resetAt: number }>();

export function loginAllowed(ip: string): boolean {
  const entry = attempts.get(ip);
  if (!entry || entry.resetAt < Date.now()) return true;
  return entry.count < MAX_ATTEMPTS;
}

export function recordLoginFailure(ip: string): void {
  const now = Date.now();
  const entry = attempts.get(ip);
  if (!entry || entry.resetAt < now) {
    attempts.set(ip, { count: 1, resetAt: now + WINDOW_MS });
  } else {
    entry.count += 1;
  }
  // Opportunistic cleanup so the map cannot grow unbounded.
  if (attempts.size > 1000) {
    for (const [key, value] of attempts) {
      if (value.resetAt < now) attempts.delete(key);
    }
  }
}

export function clearLoginFailures(ip: string): void {
  attempts.delete(ip);
}
