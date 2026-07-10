# Deploy the PRE-BUILT artifact to Hostinger (managed shared + SSH)

This is the simplest path for **Hostinger managed shared hosting** (CloudLinux
+ Passenger, the setup where `node` isn't on your SSH PATH by default). You
upload one already-built package — **no build step, no `npm install` on the
server** — set a few environment variables, and start the app.

Use the zip: **`omcomputers-net-PREBUILT-public_html.zip`**
It contains `server.mjs`, the built `dist/`, a ready-to-run `node_modules/`,
`package.json`, `schema.sql`, and the guides — everything the app needs to
run as-is on Linux.

---

## Step 1 — Create the MySQL database (hPanel)

**hPanel → Databases → MySQL Databases** → create a database and user.
Write down these four values (you'll need them in Step 4):

- **Database name**  (e.g. `u318571862_omc`)
- **Database user**  (e.g. `u318571862_omc`)
- **Password**
- **Host** — copy the exact host hPanel shows. On managed shared this is
  usually `localhost`, but **use whatever hPanel actually displays**.

---

## Step 2 — (Optional) create the tables by hand

The app creates its tables automatically on first successful connect, so you
can normally **skip this**. Do it only if your DB user lacks CREATE rights:

**hPanel → Databases → phpMyAdmin** → select your database → **SQL** tab →
paste the contents of **`schema.sql`** → **Go**. It creates 6 empty tables:
`settings`, `posts`, `price_categories`, `price_items`, `images`, `messages`.
(No content is inserted — the app fills starter content in on first run.)

---

## Step 3 — Upload and extract into public_html

1. **hPanel → Files → File Manager** → open `public_html`.
2. Upload `omcomputers-net-PREBUILT-public_html.zip`.
3. Right-click it → **Extract** → extract **into the current folder**
   (`public_html`), not a subfolder.
4. Confirm `public_html/server.mjs` and `public_html/dist/` and
   `public_html/node_modules/` exist directly. Delete the zip afterwards.

> The zip is ~50 MB and `node_modules` has thousands of files — extracting in
> File Manager is more reliable than uploading the folder unzipped. If upload
> times out, upload the zip via SSH/SFTP instead, then extract (Step 6 shows
> the SSH command).

---

## Step 4 — Set up the Node.js application (hPanel)

**hPanel → Advanced → Node.js** (or "Setup Node.js App") → **Create application**:

- **Node.js version:** `20` (or `22`)
- **Application mode:** `Production`
- **Application root:** `public_html`  *(the folder where you extracted)*
- **Application URL:** your domain (`omcomputers.net`)
- **Application startup file:** `server.mjs`

Create it. Then in the same screen, add **Environment variables**:

| Name | Value |
|---|---|
| `DB_HOST` | the host from Step 1 (usually `localhost`) |
| `DB_PORT` | `3306` |
| `DB_NAME` | database name from Step 1 |
| `DB_USER` | database user from Step 1 |
| `DB_PASSWORD` | database password from Step 1 |
| `ADMIN_PASSWORD` | a strong password you choose (for `/admin` login) |
| `ADMIN_SECRET` | a random 64-char hex string (`openssl rand -hex 32`) |
| `NODE_ENV` | `production` |
| `SITE_URL` | `https://omcomputers.net` |

> **Do NOT click "Run NPM Install"** — `node_modules` is already included and
> built for Linux. Running install is unnecessary (and if it fails on the
> shared box, it won't affect the included modules). There is also **no build
> command** to run — the app is already built.

Click **Restart** (or Start) the application.

---

## Step 5 — Verify

Open **`https://omcomputers.net/api/health`**. You want:

```json
{ "ok": true, "database": { "connected": true, "tablesReady": true, "tableCount": 6 } }
```

- If the page loads, the app is running.
- `ok: true` → MySQL is connected and the 6 tables now exist.
- If `ok: false`, read the `hint` — it names the fix (almost always a wrong
  `DB_HOST`; use the exact value from hPanel, or set `DB_SOCKET` if hPanel
  gives a socket path).

Then check the site: `https://omcomputers.net/` (logo + styling load),
`/blog`, `/parts`, and log in at `/admin` with your `ADMIN_PASSWORD`.

---

## Step 6 — SSH tips (managed shared / Passenger)

`node` isn't on your PATH by default — it lives in a per-app virtualenv that
the Node.js setup created. To use it and see real logs:

```bash
# 1) Find your app's virtualenv activation command. It is shown in
#    hPanel → Node.js → your app ("Enter to the virtual environment"),
#    and looks like:
source /home/u318571862/nodevenv/public_html/20/bin/activate && cd /home/u318571862/domains/omcomputers.net/public_html

# 2) Now `node` works:
node --version

# 3) Unzip an uploaded artifact from SSH (if you uploaded the zip via SFTP):
cd ~/domains/omcomputers.net/public_html
unzip -o omcomputers-net-PREBUILT-public_html.zip

# 4) See the app's real startup output / errors by running it directly
#    (Passenger normally runs it for you; this is just for debugging).
#    Set the env vars inline, pick any free port, then Ctrl+C when done:
DB_HOST=localhost DB_PORT=3306 DB_NAME=xxx DB_USER=xxx DB_PASSWORD=xxx \
ADMIN_PASSWORD=xxx ADMIN_SECRET=xxx NODE_ENV=production PORT=3000 \
node server.mjs
# then in another SSH session: curl http://127.0.0.1:3000/api/health
```

- To make Passenger pick up changes or new env vars, click **Restart** in
  hPanel (or `touch tmp/restart.txt` in the app root if that convention is
  enabled).
- If the site shows a Passenger error page, the real Node error is in the
  app's log (hPanel → Node.js → your app → logs, or
  `~/domains/omcomputers.net/logs/`). The `/api/health` endpoint is the
  fastest first check.

---

## Updating the site later

Content (blogs, prices, settings, images, enquiries) is edited live at
`/admin` and stored in MySQL — you do **not** redeploy for content changes.

You only re-upload a new artifact when the **code** changes. To rebuild the
artifact yourself: run the project's Linux build (see the repo) or ask for a
fresh `omcomputers-net-PREBUILT-public_html.zip`, then repeat Steps 3 and
Restart. Your database (all content) is untouched by a code redeploy.
