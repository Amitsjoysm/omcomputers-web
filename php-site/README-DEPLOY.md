# OM Computers — PHP version (deploy to Hostinger shared hosting)

This is the **PHP + MySQL** version of the site. It runs on plain Apache +
PHP — the native stack on Hostinger shared hosting — so there is **no
Node.js, no Passenger, and therefore no "Forbidden" error**. Every feature
of the original site is here: all public pages, the same theme, and the full
admin panel (login, blog editor, price list editor, site settings, enquiries
inbox, image uploads).

Nothing to build. You upload the files, set your database details, done.

---

## What's in here

```
index.php            Home
blog/                Blog listing + single post (/blog/{slug})
parts/               Price list
services/            6 service pages (/services/{slug})
contact/             Contact page + form handler (saves to DB)
admin/               Full admin panel (password-protected)
api/image.php        Serves uploaded images from the database
api/health.php       Diagnostic: shows DB connection status
inc/                 Shared code (db, auth, helpers, markdown, layout)
assets/              global.css, site.css, admin.css
icons/  logo.png  favicon.ico  site.webmanifest   Branding
seed/                Starter content (used once, on first run)
schema.sql           The 6 tables (only if you want to create them by hand)
config.php           Configuration (edit, or use config.local.php)
.htaccess            Clean URLs + security
```

---

## Step 1 — Create the MySQL database (hPanel)

**hPanel → Databases → MySQL Databases** → create a database + user. Note the
**database name, user, password, and host** (host is usually `localhost`).

**Set up the tables + starter content (recommended):** open
**phpMyAdmin** → select your database → **SQL** tab → paste the entire
contents of **`install.sql`** → **Go**. This creates all 6 tables AND fills
in the starter blog posts, price list, and your site/contact settings in one
step. It's safe to re-run (it won't duplicate data).

> Two SQL files are included: **`install.sql`** = tables **+** content (use
> this). **`schema.sql`** = empty tables only (use only if you want the app
> to seed the content itself on first load). You don't need both — pick
> `install.sql`. If you skip SQL entirely, the app also creates the tables
> and seeds content automatically on its first successful DB connection.

## Step 2 — Set your credentials

Copy **`config.local.example.php`** to **`config.local.php`** and fill in your
database details and a strong admin password:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u318571862_omc');
define('DB_USER', 'u318571862_omc');
define('DB_PASSWORD', 'your-db-password');
define('ADMIN_PASSWORD', 'your-strong-admin-password');
define('SITE_URL', 'https://omcomputers.net');
```

`config.local.php` is git-ignored and overrides `config.php`, so your
secrets stay out of the repo.

## Step 3 — Upload to public_html

- **hPanel → File Manager → public_html**.
- Upload **`omcomputers-net-PHP-public_html.zip`** and **Extract into the
  current folder** (so `public_html/index.php` exists directly — not inside
  a subfolder).
- Make sure the **`uploads/`** folder is writable (File Manager → right-click
  → Permissions → 755; it already ships writable).

That's it — there's no build step and no `npm install`. PHP runs the files
directly.

## Step 4 — Verify

- Open **`https://omcomputers.net/`** — the site loads with the logo, theme,
  services, etc.
- Open **`https://omcomputers.net/api/health.php`** — should show
  `"ok": true` and `"tableCount": 6`. If not, it tells you exactly what's
  wrong (almost always a DB detail in `config.local.php`).
- Open **`https://omcomputers.net/admin/`** — log in with your
  `ADMIN_PASSWORD`. Edit blogs, prices, settings; changes are live instantly.

---

## Editing the site (day to day)

Go to **`/admin/`**, log in, and use:

- **Blog Posts** — write in Markdown, upload cover/inline images, tick
  Published when ready.
- **Price List** — categories and products, prices, stock, photos.
- **Site Settings** — phone, WhatsApp, email, address, Google Maps, hours.
- **Enquiries** — messages sent through the contact form.

The admin URL is not linked anywhere on the public site — bookmark it.

---

## Troubleshooting

**Blog / Products / Admin show an empty page or a "Cannot connect to the
database" banner** → the site is running fine, but it can't reach MySQL yet.
The site no longer white-screens on a DB problem: public pages show their
empty state and the admin shows a red banner instead. Fix the database
details in `config.local.php` (they must exactly match hPanel → Databases),
then reload. Open **`/api/health.php`** — it prints the exact reason
(`Access denied` = wrong user/password, `Unknown database` = wrong name,
etc.). Everything starts working the moment the connection succeeds.

**The site looks unstyled / mobile menu doubled** → hard-refresh
(`Ctrl+F5`) to clear cached CSS after uploading a new version.

**A page shows "We'll be right back"** → an unexpected error was caught (you
never get a raw error/stack trace). Set `APP_DEBUG=1` in the environment (or
`define('APP_DEBUG','1')` in `config.local.php`) to see the real error while
you diagnose, then turn it off again.

---

## Why this fixes the Forbidden error

The Node.js version needed Passenger, which on shared hosting is fragile and
was returning Forbidden/unreachable. This PHP version is served **directly by
Apache** — the exact thing shared hosting is built for. There is no app
process to start, no port to bind, no startup file to configure. If PHP works
on your plan (it does), this works.

---

## Local testing (optional, for developers)

```bash
# from inside php-site/, with PHP 8+ installed and config.local.php set:
php -S localhost:8000 router.php
# open http://localhost:8000
```

`router.php` is only for PHP's built-in dev server; on Apache the `.htaccess`
handles routing and `router.php` is ignored.
