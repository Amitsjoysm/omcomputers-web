-- ─────────────────────────────────────────────────────────────────────
--  OM Computers — database schema
--  Run this ONCE against your MySQL database (hPanel → Databases →
--  phpMyAdmin → select your database → SQL tab → paste → Go).
--
--  You normally do NOT need this: the app creates these tables itself on
--  its first successful MySQL connection. Use this file only if the DB user
--  lacks CREATE privileges, or you prefer to create the tables by hand.
--
--  Safe to run more than once (every statement is IF NOT EXISTS). It does
--  NOT insert any content — the app fills in the starter blog posts, price
--  list, and settings automatically the first time it connects and finds
--  the tables empty.
-- ─────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS settings (
  id TINYINT UNSIGNED PRIMARY KEY,
  data TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posts (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS price_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL UNIQUE,
  sort INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS price_items (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL DEFAULT '',
  mime VARCHAR(100) NOT NULL,
  data MEDIUMBLOB NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  phone VARCHAR(60) NOT NULL DEFAULT '',
  service VARCHAR(120) NOT NULL DEFAULT '',
  message TEXT,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
