import { db, type RowDataPacket, type ResultSetHeader } from './db';

// ── Types ────────────────────────────────────────────────────────────
export interface SiteSettings {
  phone: string;
  whatsapp: string;
  email: string;
  address: string;
  googleMaps: string;
  openHours: string;
}

export interface PostData {
  title: string;
  excerpt: string;
  tags: string[];
  publishDate: Date;
  coverImage?: { src: string };
}

/** Shape used by PostCard / TagFilter (mirrors the old collection entry). */
export interface PostEntry {
  id: number;
  slug: string;
  data: PostData;
}

export interface PostFull extends PostEntry {
  body: string;
  published: boolean;
}

export interface PriceItem {
  id: number;
  name: string;
  brand: string;
  specs: string;
  price: number;
  inStock: boolean;
  image?: { src: string };
}

export interface PriceCategory {
  id: number;
  data: { category: string; items: PriceItem[] };
}

export const DEFAULT_SETTINGS: SiteSettings = {
  phone: '+91 98765 43210',
  whatsapp: '919876543210',
  email: 'info@omcomputers.co',
  address: 'Pune, Maharashtra',
  googleMaps: '',
  openHours: 'Monday to Saturday, 9 AM – 7 PM',
};

// ── Settings (cached briefly; busted on save) ────────────────────────
let settingsCache: { value: SiteSettings; ts: number } | null = null;
const SETTINGS_TTL_MS = 30_000;

export async function getSettings(): Promise<SiteSettings> {
  if (settingsCache && Date.now() - settingsCache.ts < SETTINGS_TTL_MS) {
    return settingsCache.value;
  }
  try {
    const pool = await db();
    const [rows] = await pool.query<RowDataPacket[]>('SELECT data FROM settings WHERE id = 1');
    const value = rows.length
      ? { ...DEFAULT_SETTINGS, ...JSON.parse(rows[0].data) }
      : DEFAULT_SETTINGS;
    settingsCache = { value, ts: Date.now() };
    return value;
  } catch (err) {
    console.error('getSettings failed, using defaults:', err);
    return settingsCache?.value ?? DEFAULT_SETTINGS;
  }
}

export async function saveSettings(data: SiteSettings): Promise<void> {
  const pool = await db();
  await pool.execute(
    'INSERT INTO settings (id, data) VALUES (1, ?) ON DUPLICATE KEY UPDATE data = VALUES(data)',
    [JSON.stringify(data)],
  );
  settingsCache = null;
}

// ── Posts ────────────────────────────────────────────────────────────
function rowToEntry(row: RowDataPacket): PostFull {
  return {
    id: row.id,
    slug: row.slug,
    body: row.body ?? '',
    published: !!row.published,
    data: {
      title: row.title,
      excerpt: row.excerpt ?? '',
      tags: row.tags ? JSON.parse(row.tags) : [],
      publishDate: row.publish_date instanceof Date ? row.publish_date : new Date(row.publish_date),
      coverImage: row.cover_image ? { src: row.cover_image } : undefined,
    },
  };
}

export async function listPublishedPosts(): Promise<PostFull[]> {
  const pool = await db();
  const [rows] = await pool.query<RowDataPacket[]>(
    `SELECT id, slug, title, excerpt, tags, cover_image, body, published, publish_date
     FROM posts WHERE published = 1 ORDER BY publish_date DESC, id DESC`,
  );
  return rows.map(rowToEntry);
}

export async function listAllPosts(): Promise<PostFull[]> {
  const pool = await db();
  const [rows] = await pool.query<RowDataPacket[]>(
    `SELECT id, slug, title, excerpt, tags, cover_image, body, published, publish_date
     FROM posts ORDER BY publish_date DESC, id DESC`,
  );
  return rows.map(rowToEntry);
}

export async function getPublishedPostBySlug(slug: string): Promise<PostFull | null> {
  const pool = await db();
  const [rows] = await pool.execute<RowDataPacket[]>(
    `SELECT id, slug, title, excerpt, tags, cover_image, body, published, publish_date
     FROM posts WHERE slug = ? AND published = 1 LIMIT 1`, [slug],
  );
  return rows.length ? rowToEntry(rows[0]) : null;
}

export async function getPostById(id: number): Promise<PostFull | null> {
  const pool = await db();
  const [rows] = await pool.execute<RowDataPacket[]>(
    `SELECT id, slug, title, excerpt, tags, cover_image, body, published, publish_date
     FROM posts WHERE id = ? LIMIT 1`, [id],
  );
  return rows.length ? rowToEntry(rows[0]) : null;
}

export interface PostInput {
  slug: string;
  title: string;
  excerpt: string;
  tags: string[];
  coverImage: string | null;
  body: string;
  published: boolean;
  publishDate: string; // YYYY-MM-DD
}

export async function createPost(input: PostInput): Promise<number> {
  const pool = await db();
  const [res] = await pool.execute<ResultSetHeader>(
    `INSERT INTO posts (slug, title, excerpt, tags, cover_image, body, published, publish_date)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
    [input.slug, input.title, input.excerpt, JSON.stringify(input.tags),
     input.coverImage, input.body, input.published ? 1 : 0, input.publishDate],
  );
  return res.insertId;
}

export async function updatePost(id: number, input: PostInput): Promise<void> {
  const pool = await db();
  await pool.execute(
    `UPDATE posts SET slug = ?, title = ?, excerpt = ?, tags = ?, cover_image = ?,
     body = ?, published = ?, publish_date = ? WHERE id = ?`,
    [input.slug, input.title, input.excerpt, JSON.stringify(input.tags),
     input.coverImage, input.body, input.published ? 1 : 0, input.publishDate, id],
  );
}

export async function deletePost(id: number): Promise<void> {
  const pool = await db();
  await pool.execute('DELETE FROM posts WHERE id = ?', [id]);
}

export async function slugExists(slug: string, excludeId?: number): Promise<boolean> {
  const pool = await db();
  const [rows] = await pool.execute<RowDataPacket[]>(
    excludeId
      ? 'SELECT id FROM posts WHERE slug = ? AND id <> ? LIMIT 1'
      : 'SELECT id FROM posts WHERE slug = ? LIMIT 1',
    excludeId ? [slug, excludeId] : [slug],
  );
  return rows.length > 0;
}

// ── Prices ───────────────────────────────────────────────────────────
function rowToItem(row: RowDataPacket): PriceItem {
  return {
    id: row.id,
    name: row.name,
    brand: row.brand ?? '',
    specs: row.specs ?? '',
    price: Number(row.price),
    inStock: !!row.in_stock,
    image: row.image ? { src: row.image } : undefined,
  };
}

export async function listCategoriesWithItems(): Promise<PriceCategory[]> {
  const pool = await db();
  const [cats] = await pool.query<RowDataPacket[]>(
    'SELECT id, name FROM price_categories ORDER BY sort, name',
  );
  const [items] = await pool.query<RowDataPacket[]>(
    `SELECT id, category_id, name, brand, specs, price, in_stock, image
     FROM price_items ORDER BY sort, id`,
  );
  return cats.map(cat => ({
    id: cat.id,
    data: {
      category: cat.name,
      items: items.filter(i => i.category_id === cat.id).map(rowToItem),
    },
  }));
}

export async function getCategory(id: number): Promise<{ id: number; name: string } | null> {
  const pool = await db();
  const [rows] = await pool.execute<RowDataPacket[]>(
    'SELECT id, name FROM price_categories WHERE id = ? LIMIT 1', [id],
  );
  return rows.length ? { id: rows[0].id, name: rows[0].name } : null;
}

export async function createCategory(name: string): Promise<number> {
  const pool = await db();
  const [res] = await pool.execute<ResultSetHeader>(
    'INSERT INTO price_categories (name, sort) VALUES (?, (SELECT COALESCE(MAX(c.sort),0)+1 FROM price_categories c))',
    [name],
  );
  return res.insertId;
}

export async function renameCategory(id: number, name: string): Promise<void> {
  const pool = await db();
  await pool.execute('UPDATE price_categories SET name = ? WHERE id = ?', [name, id]);
}

export async function deleteCategory(id: number): Promise<void> {
  const pool = await db();
  await pool.execute('DELETE FROM price_categories WHERE id = ?', [id]);
}

export interface ItemInput {
  name: string;
  brand: string;
  specs: string;
  price: number;
  inStock: boolean;
  image: string | null;
}

export async function createItem(categoryId: number, input: ItemInput): Promise<void> {
  const pool = await db();
  await pool.execute(
    `INSERT INTO price_items (category_id, name, brand, specs, price, in_stock, image, sort)
     VALUES (?, ?, ?, ?, ?, ?, ?, (SELECT COALESCE(MAX(i.sort),0)+1 FROM price_items i WHERE i.category_id = ?))`,
    [categoryId, input.name, input.brand, input.specs, input.price, input.inStock ? 1 : 0, input.image, categoryId],
  );
}

export async function updateItem(id: number, input: ItemInput): Promise<void> {
  const pool = await db();
  await pool.execute(
    `UPDATE price_items SET name = ?, brand = ?, specs = ?, price = ?, in_stock = ?, image = ?
     WHERE id = ?`,
    [input.name, input.brand, input.specs, input.price, input.inStock ? 1 : 0, input.image, id],
  );
}

export async function getItem(id: number): Promise<(PriceItem & { categoryId: number }) | null> {
  const pool = await db();
  const [rows] = await pool.execute<RowDataPacket[]>(
    `SELECT id, category_id, name, brand, specs, price, in_stock, image
     FROM price_items WHERE id = ? LIMIT 1`, [id],
  );
  if (!rows.length) return null;
  return { ...rowToItem(rows[0]), categoryId: rows[0].category_id };
}

export async function deleteItem(id: number): Promise<void> {
  const pool = await db();
  await pool.execute('DELETE FROM price_items WHERE id = ?', [id]);
}

// ── Contact messages (enquiries from the contact form) ───────────────
export interface ContactMessage {
  id: number;
  name: string;
  phone: string;
  service: string;
  message: string;
  isRead: boolean;
  createdAt: Date;
}

export async function createMessage(
  input: { name: string; phone: string; service: string; message: string },
): Promise<void> {
  const pool = await db();
  await pool.execute(
    'INSERT INTO messages (name, phone, service, message) VALUES (?, ?, ?, ?)',
    [input.name.slice(0, 191), input.phone.slice(0, 60), input.service.slice(0, 120), input.message.slice(0, 5000)],
  );
}

export async function listMessages(): Promise<ContactMessage[]> {
  const pool = await db();
  const [rows] = await pool.query<RowDataPacket[]>(
    'SELECT id, name, phone, service, message, is_read, created_at FROM messages ORDER BY created_at DESC, id DESC LIMIT 500',
  );
  return rows.map(r => ({
    id: r.id, name: r.name, phone: r.phone, service: r.service,
    message: r.message ?? '', isRead: !!r.is_read,
    createdAt: r.created_at instanceof Date ? r.created_at : new Date(r.created_at),
  }));
}

export async function countUnreadMessages(): Promise<number> {
  const pool = await db();
  const [rows] = await pool.query<RowDataPacket[]>('SELECT COUNT(*) AS n FROM messages WHERE is_read = 0');
  return Number(rows[0].n);
}

export async function markMessageRead(id: number, isRead: boolean): Promise<void> {
  const pool = await db();
  await pool.execute('UPDATE messages SET is_read = ? WHERE id = ?', [isRead ? 1 : 0, id]);
}

export async function deleteMessage(id: number): Promise<void> {
  const pool = await db();
  await pool.execute('DELETE FROM messages WHERE id = ?', [id]);
}

// ── Images (stored in DB so they survive redeploys) ──────────────────
export async function saveImage(filename: string, mime: string, data: Buffer): Promise<number> {
  const pool = await db();
  const [res] = await pool.execute<ResultSetHeader>(
    'INSERT INTO images (filename, mime, data) VALUES (?, ?, ?)',
    [filename, mime, data],
  );
  return res.insertId;
}

export async function getImage(id: number): Promise<{ mime: string; data: Buffer } | null> {
  const pool = await db();
  const [rows] = await pool.execute<RowDataPacket[]>(
    'SELECT mime, data FROM images WHERE id = ? LIMIT 1', [id],
  );
  return rows.length ? { mime: rows[0].mime, data: rows[0].data } : null;
}
