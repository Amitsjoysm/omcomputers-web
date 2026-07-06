# Deploying OM Computers to Hostinger (Business Plan)

Step-by-step guide to deploy `omcomputers-web` as a **Node.js web app** on the
Hostinger Business plan, with a self-contained admin panel at
`https://your-domain.com/admin` for editing all site content.

**No GitHub. No third-party services. No external database.** Everything —
the website, the admin panel, and the content database — runs inside your
Hostinger account.

---

## How it works (read this first)

```
  You (admin)                     Your Hostinger account
  ───────────                     ───────────────────────
  your-domain.com/admin  ──────►  Node.js app  ──────►  MySQL database
  log in with a password          (the website)         (blogs, prices,
  edit blogs / prices /                                  settings, images)
  contact details / images
        │
        └──► changes appear on the public site immediately (no rebuild, no wait)
```

- **The admin logs in with a password you choose** — not a GitHub account,
  not any external login. You are in full control.
- **All content lives in a MySQL database** on Hostinger (included in your
  Business plan). Blog posts, price lists, site settings, and uploaded
  images are all stored there and survive redeploys.
- **Edits are instant.** The site is server-rendered, so saving in the admin
  updates the live pages right away — no build step, no external service.
- **The admin panel is hidden.** There is no link to it anywhere on the
  public website. Only someone who knows the `/admin` address *and* the
  password can get in.

---

## Prerequisites

- Hostinger **Business Web Hosting** plan (supports Node.js apps + MySQL)
- Your domain added to Hostinger
- The deployment zip: `omcomputers-web-hostinger-source.zip`

---

## Step 1 — Create the MySQL database

1. In **hPanel** → **Databases** → **Management** (MySQL Databases).
2. Create a new database. Note down the four values it gives you:
   - **Database name** (e.g. `u123456789_omc`)
   - **Database user** (e.g. `u123456789_omc`)
   - **Password** (the one you set)
   - **Host** — on Hostinger this is almost always `localhost`
3. Keep these for Step 3. You do **not** need to create any tables — the app
   creates them automatically on first run and fills them with the starter
   content (the two sample blog posts and the price lists).

---

## Step 2 — Generate the admin secret

You need one random string to sign login sessions. In PowerShell on your PC:

```powershell
-join ((1..64) | ForEach-Object { '{0:x}' -f (Get-Random -Max 16) })
```

(or on Mac/Linux: `openssl rand -hex 32`)

Copy the output — this is your `ADMIN_SECRET` for Step 3.
Also decide on an **admin password** (long and unique) — this is what you
will type to log in.

---

## Step 3 — Create the Node.js web app

1. In **hPanel** → **Websites** → **Add website** → **Web app (Node.js)**.
2. Choose **Upload files (ZIP)** and upload
   `omcomputers-web-hostinger-source.zip`.
   *(You may also deploy from GitHub if you ever want to — it is optional and
   not required.)*
3. Hostinger auto-detects **Astro**. Confirm these settings:
   - **Build command:** `npm run build`
   - **Start command:** `npm run start`
   - **Node.js version:** `20` or `22`
4. Open the app's **Environment variables** section and add all of these:

   | Name | Value |
   |---|---|
   | `DB_HOST` | `localhost` |
   | `DB_PORT` | `3306` |
   | `DB_NAME` | database name from Step 1 |
   | `DB_USER` | database user from Step 1 |
   | `DB_PASSWORD` | database password from Step 1 |
   | `ADMIN_PASSWORD` | the login password you chose in Step 2 |
   | `ADMIN_SECRET` | the random string from Step 2 |
   | `NODE_ENV` | `production` |
   | `SITE_URL` | `https://your-domain.com` |

5. Click **Deploy** and wait for the build to finish. On first start the app
   creates the database tables and loads the starter content automatically.

---

## Step 4 — Connect the domain + SSL

1. In hPanel, open the web app → **Domain** → assign your domain
   (and the `www.` version).
2. If the domain's DNS is managed elsewhere, point it at Hostinger
   (nameservers, or the A record hPanel shows you).
3. Enable the free **SSL certificate** (Let's Encrypt) and turn on
   **Force HTTPS**.

---

## Step 5 — Verify

Open your site and check:

- [ ] `https://your-domain.com/` — loads with the OM Computers logo
- [ ] Browser tab shows the favicon (hard-refresh with `Ctrl+F5`)
- [ ] `/blog` shows the two starter posts; a post opens and reads correctly
- [ ] `/parts` shows the price categories and products
- [ ] Footer has **no** "Admin" link
- [ ] `/admin` redirects to a login page
- [ ] Log in with your `ADMIN_PASSWORD` → dashboard appears
- [ ] Edit **Site Settings** (e.g. opening hours), Save, then refresh the
      public site — the change shows immediately
- [ ] Write a test blog post, tick **Published**, Save → it appears on `/blog`

---

## Daily use — editing the site

1. Go to `https://your-domain.com/admin` (bookmark it — it is not linked
   anywhere on the site).
2. Log in with your password.
3. Edit:
   - **Blog Posts** — write posts in simple Markdown, upload cover and inline
     images, tick **Published** when ready (unticked = hidden draft)
   - **Price List** — add/edit categories and products, prices, stock status,
     product photos
   - **Site Settings** — phone, WhatsApp, email, address, Google Maps,
     opening hours
4. Click **Save**. The change is live on the website immediately.

**Images** you upload are stored in the database and served at
`/api/images/<id>`, so they survive every redeploy.

**Changing the password later:** edit the `ADMIN_PASSWORD` environment
variable in hPanel and restart the app.

---

## Backups & safety

- **Back up your content** from hPanel → Databases → phpMyAdmin → Export
  (or Hostinger's automatic backups). That single export contains every blog
  post, price, setting, and image.
- Login is password-protected, rate-limited (8 tries per 15 minutes per
  visitor), and the admin pages are marked `noindex` so search engines never
  list them.

---

## Maintenance notes

- **Changing the logo:** replace `public/logo.png`, run `npm run setup`
  locally (regenerates every favicon size, `favicon.ico`, and the social
  share image), then re-deploy.
- **Starter content** lives in the `seed/` folder and is only used the very
  first time the database is empty. After that, everything is edited through
  `/admin` and stored in MySQL — the seed files are never used again and
  never overwrite your edits.
- **Environment variables** are the only configuration. There are no secrets
  in the code.
