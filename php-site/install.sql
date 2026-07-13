-- ═══════════════════════════════════════════════════════════════════
-- OM Computers — full install (tables + starter content)
-- Run ONCE on your new, empty MySQL database in phpMyAdmin:
--   phpMyAdmin → select your database → SQL tab → paste all of this → Go
-- Safe to re-run: tables use IF NOT EXISTS and rows use INSERT IGNORE /
-- ON DUPLICATE KEY UPDATE, so existing data is not duplicated.
-- ═══════════════════════════════════════════════════════════════════
SET NAMES utf8mb4;
SET time_zone = '+05:30';

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
  CONSTRAINT fk_item_category FOREIGN KEY (category_id) REFERENCES price_categories(id) ON DELETE CASCADE
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

-- ── Site settings ──
INSERT INTO settings (id, data) VALUES (1, '{"phone":"+91 98224 61023","whatsapp":"919822461023","email":"info@omcomputers.net","address":"12, Vaidyabaug chs, Kamat Ln, Alibag, Maharashtra 402201","googleMaps":"","openHours":"Monday to Saturday, 9 AM – 7 PM"}')
  ON DUPLICATE KEY UPDATE data = VALUES(data);

-- ── Price list ──
INSERT IGNORE INTO price_categories (id, name, sort) VALUES (1, 'Laptops', 1);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (1, 1, 'Lenovo IdeaPad Slim 3', 'Lenovo', 'Intel i3-12th Gen · 8 GB RAM · 512 GB SSD · 15.6" FHD · Windows 11', 38000, 1, 0);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (2, 1, 'HP Pavilion 15', 'HP', 'AMD Ryzen 5 · 8 GB RAM · 512 GB SSD · 15.6" FHD · Windows 11', 42000, 1, 1);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (3, 1, 'Dell Inspiron 15', 'Dell', 'Intel i5-12th Gen · 16 GB RAM · 512 GB SSD · 15.6" FHD · Windows 11', 55000, 1, 2);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (4, 1, 'ASUS VivoBook 16', 'ASUS', 'Intel i7-12th Gen · 16 GB RAM · 1 TB SSD · 16" WUXGA · Windows 11', 68000, 0, 3);

INSERT IGNORE INTO price_categories (id, name, sort) VALUES (2, 'Processors', 2);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (5, 2, 'Intel Core i5-13400F', 'Intel', '10-Core · 2.5 GHz base · 4.6 GHz boost · LGA1700 socket · 65W TDP', 15500, 1, 0);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (6, 2, 'Intel Core i7-13700K', 'Intel', '16-Core · 3.4 GHz base · 5.4 GHz boost · LGA1700 socket · 125W TDP', 34000, 1, 1);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (7, 2, 'AMD Ryzen 5 7600X', 'AMD', '6-Core · 4.7 GHz base · 5.3 GHz boost · AM5 socket · 105W TDP', 21000, 1, 2);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (8, 2, 'AMD Ryzen 7 7700X', 'AMD', '8-Core · 4.5 GHz base · 5.4 GHz boost · AM5 socket · 105W TDP', 29500, 0, 3);

INSERT IGNORE INTO price_categories (id, name, sort) VALUES (3, 'Storage', 3);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (9, 3, 'Kingston A400 SSD', 'Kingston', '480 GB · SATA III · 2.5" · 500 MB/s Read · 450 MB/s Write', 3200, 1, 0);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (10, 3, 'Samsung 870 EVO', 'Samsung', '1 TB · SATA III · 2.5" · 560 MB/s Read · 530 MB/s Write', 7500, 1, 1);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (11, 3, 'WD Blue SN570 NVMe', 'Western Digital', '1 TB · NVMe PCIe 3.0 · M.2 2280 · 3500 MB/s Read · 3000 MB/s Write', 6800, 1, 2);
INSERT IGNORE INTO price_items (id, category_id, name, brand, specs, price, in_stock, sort) VALUES (12, 3, 'Seagate Barracuda HDD', 'Seagate', '2 TB · SATA III · 3.5" · 7200 RPM · 256 MB Cache', 4200, 1, 3);

-- ── Blog posts ──
INSERT IGNORE INTO posts (id, slug, title, excerpt, tags, body, published, publish_date) VALUES (1, 'cctv-guide-for-small-businesses', 'CCTV Guide for Small Businesses in Pune', 'Everything a small business owner needs to know before installing CCTV cameras.', '["CCTV","Business","Security"]', '## Why Every Small Business Needs CCTV

CCTV cameras do more than deter theft. They protect you legally, help you monitor operations remotely, and give your customers confidence.

In Pune, small businesses face rising shoplifting, employee disputes, and break-ins. A well-designed CCTV system costs less than one incident.

## How Many Cameras Do You Need?

| Business Type | Minimum Cameras | Recommended |
|---|---|---|
| Shop (under 500 sq ft) | 2 | 4 |
| Restaurant | 3 | 6 |
| Office (10 people) | 2 | 4 |
| Warehouse | 4 | 8 |

Always cover: entrance/exit, cash counter, stockroom, and parking if applicable.

## Indoor vs Outdoor Cameras

**Indoor cameras** need:
- Good low-light performance (IR night vision)
- Wide-angle lens (90°–120°) for room coverage
- Dome design to discourage tampering

**Outdoor cameras** need:
- IP66 or higher weatherproof rating
- IR night vision range of at least 20 metres
- Vandal-proof housing

## DVR vs NVR: Which Should You Choose?

- **DVR (Digital Video Recorder):** Works with analog cameras. More affordable. Good for basic setups.
- **NVR (Network Video Recorder):** Works with IP cameras. Higher resolution (up to 4K). Remote viewing over internet.

For most Pune small businesses, we recommend **IP cameras + NVR**. The resolution advantage is significant and prices have come down sharply.

## Remote Viewing Setup

With an NVR system, you can watch live footage on your smartphone from anywhere:

1. Connect NVR to your router
2. Install the manufacturer\'s app (Hikvision, Dahua, etc.)
3. Add the NVR using its UID or IP address
4. Watch live or recorded footage anytime

> **OM Computers handles the entire setup.** We configure remote viewing so you can monitor your shop from home on day one.

## AMC: Annual Maintenance Contract

CCTV systems need periodic maintenance:

- Cleaning camera lenses (dust and spider webs block IR)
- Checking hard drive health
- Updating firmware
- Verifying remote access still works

Our AMC plans start at ₹2,500/year for 4-camera systems. Call us to get a custom quote.', 1, '2025-04-20');

INSERT IGNORE INTO posts (id, slug, title, excerpt, tags, body, published, publish_date) VALUES (2, 'how-to-extend-laptop-life', 'How to Extend the Life of Your Laptop', 'Simple habits that can add years to your laptop\'s life and save you money.', '["Repair","Tips","Laptops"]', '## Keep It Cool

Overheating is the number-one killer of laptops. Here is what to do:

- Clean the vents every 3–6 months with compressed air
- Use a cooling pad if you run heavy software
- Never block the vents by placing the laptop on soft surfaces

## Battery Care

Avoid draining to 0% regularly. Plug in when you reach around 20%.

- Avoid charging to 100% constantly if your laptop supports battery health modes
- Store the laptop at around 50% charge if you won\'t use it for weeks
- Replace aging batteries before they swell. Swollen batteries can damage the chassis

## Software Hygiene

A slow laptop often just needs a clean-up:

- Remove programs you don\'t use
- Run disk cleanup and delete temporary files monthly
- Keep Windows and drivers updated to avoid security vulnerabilities
- Consider a fresh OS install every 2–3 years

## Screen and Body Care

- Use a microfibre cloth (not paper towels) to clean the screen
- Keep food and liquids away from the keyboard
- Use a padded sleeve or bag when carrying the laptop

> **OM Computers Tip:** If your laptop takes more than 60 seconds to boot, bring it in for a free diagnosis. Often a ₹3,000 SSD upgrade makes it feel brand new.

## When to Get Professional Help

Bring your laptop to us if you notice:

1. Loud fan noise that won\'t stop
2. Screen flickering or dead pixels
3. Battery draining in under 2 hours
4. Keyboard keys sticking or not responding
5. Overheating even during light use

We offer **same-day diagnosis** and **6-month warranty** on all repairs using genuine parts.', 1, '2025-05-12');

-- Done. Your site now has 6 tables + starter content.
