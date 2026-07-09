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
  return 'https://omcomputers.co';
}

// Deployed on Hostinger Business (Node.js web app).
// All pages are server-rendered so content edits made in the /admin panel
// (stored in MySQL) appear on the site immediately — no rebuild needed.
export default defineConfig({
  site: resolveSite(),
  output: 'server',
  adapter: node({ mode: 'standalone' }),
  integrations: [react()],
  redirects: {
    '/prices': '/parts',
  },
});
