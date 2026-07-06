import mysql from 'mysql2/promise';
import { env } from './env';
import { seedPosts, seedPriceCategories, seedSettings } from './seed-data';

let pool: mysql.Pool | null = null;
let initPromise: Promise<void> | null = null;

function getPool(): mysql.Pool {
  if (!pool) {
    pool = mysql.createPool({
      host: env('DB_HOST', '127.0.0.1'),
      port: Number(env('DB_PORT', '3306')),
      user: env('DB_USER'),
      password: env('DB_PASSWORD'),
      database: env('DB_NAME'),
      charset: 'utf8mb4',
      connectionLimit: 5,
      // Hostinger MySQL closes idle connections; keep the pool honest.
      enableKeepAlive: true,
      keepAliveInitialDelay: 10_000,
    });
  }
  return pool;
}

const SCHEMA = [
  `CREATE TABLE IF NOT EXISTS settings (
     id TINYINT UNSIGNED PRIMARY KEY,
     data TEXT NOT NULL
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`,
  `CREATE TABLE IF NOT EXISTS posts (
     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     slug VARCHAR(191) NOT NULL UNIQUE,
     title VARCHAR(255) NOT NULL,
     excerpt VARCHAR(500) NOT NULL DEFAULT '',
     tags TEXT,
     cover_image VARCHAR(255) DEFAULT NULL,
     body MEDIUMTEXT,
     published TINYINT(1) NOT NULL DEFAULT 0,
     publish_date DATE NOT NULL,
     created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`,
  `CREATE TABLE IF NOT EXISTS price_categories (
     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(191) NOT NULL UNIQUE,
     sort INT NOT NULL DEFAULT 0
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`,
  `CREATE TABLE IF NOT EXISTS price_items (
     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     category_id INT UNSIGNED NOT NULL,
     name VARCHAR(255) NOT NULL,
     brand VARCHAR(191) NOT NULL DEFAULT '',
     specs TEXT,
     price DECIMAL(10,2) NOT NULL DEFAULT 0,
     in_stock TINYINT(1) NOT NULL DEFAULT 1,
     image VARCHAR(255) DEFAULT NULL,
     sort INT NOT NULL DEFAULT 0,
     CONSTRAINT fk_item_category FOREIGN KEY (category_id)
       REFERENCES price_categories(id) ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`,
  `CREATE TABLE IF NOT EXISTS images (
     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     filename VARCHAR(255) NOT NULL DEFAULT '',
     mime VARCHAR(100) NOT NULL,
     data MEDIUMBLOB NOT NULL,
     created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`,
  `CREATE TABLE IF NOT EXISTS messages (
     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(191) NOT NULL,
     phone VARCHAR(60) NOT NULL DEFAULT '',
     service VARCHAR(120) NOT NULL DEFAULT '',
     message TEXT,
     is_read TINYINT(1) NOT NULL DEFAULT 0,
     created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`,
];

async function initDb(): Promise<void> {
  const p = getPool();
  for (const stmt of SCHEMA) await p.query(stmt);

  // Seed only when empty — never overwrite admin edits.
  const [postRows] = await p.query<mysql.RowDataPacket[]>('SELECT COUNT(*) AS n FROM posts');
  if (postRows[0].n === 0) {
    for (const post of seedPosts()) {
      await p.execute(
        `INSERT INTO posts (slug, title, excerpt, tags, body, published, publish_date)
         VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [post.slug, post.title, post.excerpt, JSON.stringify(post.tags), post.body, post.published ? 1 : 0, post.publishDate],
      );
    }
  }

  const [catRows] = await p.query<mysql.RowDataPacket[]>('SELECT COUNT(*) AS n FROM price_categories');
  if (catRows[0].n === 0) {
    let sort = 0;
    for (const cat of seedPriceCategories()) {
      const [res] = await p.execute<mysql.ResultSetHeader>(
        'INSERT INTO price_categories (name, sort) VALUES (?, ?)', [cat.category, sort++],
      );
      let itemSort = 0;
      for (const item of cat.items) {
        await p.execute(
          `INSERT INTO price_items (category_id, name, brand, specs, price, in_stock, sort)
           VALUES (?, ?, ?, ?, ?, ?, ?)`,
          [res.insertId, item.name, item.brand ?? '', item.specs ?? '', item.price ?? 0, item.inStock === false ? 0 : 1, itemSort++],
        );
      }
    }
  }

  const [settingRows] = await p.query<mysql.RowDataPacket[]>('SELECT COUNT(*) AS n FROM settings WHERE id = 1');
  if (settingRows[0].n === 0) {
    await p.execute('INSERT INTO settings (id, data) VALUES (1, ?)', [JSON.stringify(seedSettings())]);
  }
}

/** Returns the connection pool, creating tables + seed data on first use. */
export async function db(): Promise<mysql.Pool> {
  if (!initPromise) {
    initPromise = initDb().catch(err => {
      initPromise = null; // allow retry on next request
      throw err;
    });
  }
  await initPromise;
  return getPool();
}

export type { RowDataPacket, ResultSetHeader } from 'mysql2/promise';
