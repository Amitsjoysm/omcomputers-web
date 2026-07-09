import { defineMiddleware } from 'astro:middleware';
import { SESSION_COOKIE, verifySessionToken } from './lib/auth';

// Fully percent-decode a path (handling single AND multi-layer encoding),
// then lowercase it. This defends the /admin guard below against auth-bypass
// tricks where an encoded or mixed-case path (e.g. `/%61dmin`, `/ADMIN`,
// double-encoded variants) slips past a naive `startsWith('/admin')` check
// while still routing to an admin page.
function normalizePath(pathname: string): string {
  let p = pathname;
  for (let i = 0; i < 5; i++) {
    let decoded: string;
    try {
      decoded = decodeURIComponent(p);
    } catch {
      break; // malformed encoding — stop and use what we have
    }
    if (decoded === p) break;
    p = decoded;
  }
  return p.toLowerCase();
}

// Every /admin page except the login screen requires a valid session.
export const onRequest = defineMiddleware((context, next) => {
  const raw = context.url.pathname;
  const norm = normalizePath(raw);

  // Guard if the raw OR any decoded/normalized form targets /admin.
  const isAdmin = raw.startsWith('/admin') || norm.startsWith('/admin');
  if (!isAdmin) return next();

  // The login page is the only public /admin route (exact, unencoded path).
  if (raw === '/admin/login' || raw === '/admin/login/') return next();

  const token = context.cookies.get(SESSION_COOKIE)?.value;
  if (!verifySessionToken(token)) {
    return context.redirect('/admin/login');
  }
  return next();
});
