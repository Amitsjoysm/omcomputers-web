import { defineConfig } from 'astro/config';
import netlify   from '@astrojs/netlify';
import react     from '@astrojs/react';
import mdx       from '@astrojs/mdx';
import keystatic from '@keystatic/astro';

export default defineConfig({
  site: 'https://omcomputers.co',
  output: 'hybrid',
  adapter: netlify(),
  integrations: [react(), mdx(), keystatic()],
});
