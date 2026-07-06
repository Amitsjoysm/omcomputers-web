// Initial content bundled into the server at build time. It is inserted
// into the database only when the tables are empty (first boot), so edits
// made in /admin are never overwritten.
import settingsJson from '../../seed/settings.json';

const blogRaw = import.meta.glob('../../seed/blog/*.md', {
  query: '?raw',
  import: 'default',
  eager: true,
}) as Record<string, string>;

const priceJson = import.meta.glob('../../seed/prices/*.json', {
  import: 'default',
  eager: true,
}) as Record<string, { category: string; items: SeedPriceItem[] }>;

export interface SeedPriceItem {
  name: string;
  brand?: string;
  specs?: string;
  price?: number;
  inStock?: boolean;
}

export interface SeedPost {
  slug: string;
  title: string;
  excerpt: string;
  tags: string[];
  published: boolean;
  publishDate: string; // YYYY-MM-DD
  body: string;        // markdown
}

// Minimal frontmatter parser for the seed files (key: value, [a, b] lists,
// booleans, dates). Not a general YAML parser — seed files only.
function parseFrontmatter(raw: string): { meta: Record<string, unknown>; body: string } {
  const match = raw.match(/^---\r?\n([\s\S]*?)\r?\n---\r?\n?/);
  if (!match) return { meta: {}, body: raw };
  const meta: Record<string, unknown> = {};
  for (const line of match[1].split(/\r?\n/)) {
    const idx = line.indexOf(':');
    if (idx === -1) continue;
    const key = line.slice(0, idx).trim();
    let value: unknown = line.slice(idx + 1).trim();
    const str = value as string;
    if (str.startsWith('[') && str.endsWith(']')) {
      value = str.slice(1, -1).split(',').map(s => s.trim().replace(/^["']|["']$/g, '')).filter(Boolean);
    } else if (str === 'true' || str === 'false') {
      value = str === 'true';
    } else {
      value = str.replace(/^["']|["']$/g, '');
    }
    meta[key] = value;
  }
  return { meta, body: raw.slice(match[0].length) };
}

export function seedPosts(): SeedPost[] {
  return Object.entries(blogRaw).map(([path, raw]) => {
    const { meta, body } = parseFrontmatter(raw);
    const slug = path.split('/').pop()!.replace(/\.md$/, '');
    return {
      slug,
      title: String(meta.title ?? slug),
      excerpt: String(meta.excerpt ?? ''),
      tags: Array.isArray(meta.tags) ? (meta.tags as string[]) : [],
      published: meta.published === true,
      publishDate: String(meta.publishDate ?? new Date().toISOString().slice(0, 10)),
      body: body.trim(),
    };
  });
}

export function seedPriceCategories(): { category: string; items: SeedPriceItem[] }[] {
  return Object.values(priceJson);
}

export function seedSettings(): Record<string, string> {
  return settingsJson as Record<string, string>;
}
