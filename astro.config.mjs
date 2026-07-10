import { defineConfig } from 'astro/config';
import node  from '@astrojs/node';
import react from '@astrojs/react';

// Resolve the canonical site URL from SITE_URL if it is a valid absolute
// URL, otherwise fall back to the production domain. Wrapped so a missing or
// malformed SITE_URL can never fail the build.
function resolveSite() {
  const raw = process.env.SITE_URL;
  if (raw) {
    try {
      return new URL(raw).origin;
    } catch {
      // ignore malformed value, use fallback below
    }
  }
  return 'https://omcomputers.net';
}

// Deployed on Hostinger Business (managed shared / CloudLinux + Passenger).
// All pages are server-rendered so content edits made in the /admin panel
// (stored in MySQL) appear on the site immediately — no rebuild needed.
//
// Middleware mode (not standalone): the built server (dist/server/entry.mjs)
// exports a request handler instead of self-starting a server. The custom
// root `server.mjs` wraps it — serving static files and listening on the
// port OR Unix-socket path the host provides. This is what makes the app
// compatible with Phusion Passenger (Hostinger shared hosting hands the app
// a socket path, which the standalone server cannot consume) while staying
// portable: static assets resolve relative to server.mjs at runtime, so a
// pre-built dist/ can be uploaded and run from any directory.
export default defineConfig({
  site: resolveSite(),
  output: 'server',
  adapter: node({ mode: 'middleware' }),
  integrations: [react()],
  redirects: {
    '/prices': '/parts',
  },
});
