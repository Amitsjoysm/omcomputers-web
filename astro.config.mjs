import { defineConfig } from 'astro/config';
import node  from '@astrojs/node';
import react from '@astrojs/react';

// Deployed on Hostinger Business (Node.js web app).
// All pages are server-rendered so content edits made in the /admin panel
// (stored in MySQL) appear on the site immediately — no rebuild needed.
export default defineConfig({
  site: 'https://omcomputers.co',
  output: 'server',
  adapter: node({ mode: 'standalone' }),
  integrations: [react()],
  redirects: {
    '/prices': '/parts',
  },
});
