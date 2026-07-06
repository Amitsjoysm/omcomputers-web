import type { APIRoute } from 'astro';
import { listPublishedPosts } from '../lib/content';

export const GET: APIRoute = async ({ site }) => {
  const base = site?.origin ?? 'https://omcomputers.co';

  const posts = await listPublishedPosts();

  const staticPages = [
    { path: '',                             priority: '1.0' },
    { path: '/blog',                        priority: '0.8' },
    { path: '/parts',                       priority: '0.8' },
    { path: '/contact',                     priority: '0.8' },
    { path: '/services/device-repair',      priority: '0.9' },
    { path: '/services/cctv-surveillance',  priority: '0.9' },
    { path: '/services/biometric-access',   priority: '0.9' },
    { path: '/services/it-support-amc',     priority: '0.9' },
    { path: '/services/web-digital',        priority: '0.9' },
    { path: '/services/hardware-sales',     priority: '0.9' },
  ];

  const today = new Date().toISOString().split('T')[0];

  const blogUrls = posts.map(p => ({
    url: `/blog/${p.slug}`,
    lastmod: p.data.publishDate.toISOString().split('T')[0],
    priority: '0.7',
  }));

  const allUrls = [
    ...staticPages.map(({ path, priority }) => ({
      url: path || '/',
      lastmod: today,
      priority,
    })),
    ...blogUrls,
  ];

  const xml = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${allUrls.map(u => `  <url>
    <loc>${base}${u.url}</loc>
    <lastmod>${u.lastmod}</lastmod>
    <priority>${u.priority}</priority>
  </url>`).join('\n')}
</urlset>`;

  return new Response(xml, {
    headers: {
      'Content-Type': 'application/xml; charset=utf-8',
      'Cache-Control': 'public, max-age=3600',
    },
  });
};
