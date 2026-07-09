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
- The deployment zip: **`omcomputers-web-public-html.zip`** — this one
  extracts flat, with `package.json` directly at the top level, ready to
  drop straight into `public_html` (or clone the GitHub repo:
  `https://github.com/Amitsjoysm/omcomputers-web` — private, `main` branch)

> ⚠️ **Getting a "Forbidden" error after uploading?** Almost always caused
> by the zip extracting into a nested subfolder so Hostinger can't find
> `package.json` at the app root. See **`FIX-403-FORBIDDEN.md`** for the
> full walkthrough — the short version is: use the flat zip above, and
> confirm `public_html/package.json` exists directly (not one folder
> deeper) after extracting.

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
2. Choose one of:
   - **Upload files (ZIP)** and upload `omcomputers-web-public-html.zip`,
     then **extract it directly into the Application Root** (not into a new
     subfolder — see the warning above and `FIX-403-FORBIDDEN.md` if
     unsure), **or**
   - **Deploy from GitHub** and connect the private repo
     `Amitsjoysm/omcomputers-web` (branch `main`) — this also lets Hostinger
     auto-redeploy whenever new code is pushed to that branch. You'll need to
     authorize Hostinger's GitHub App and grant it access to this repo.
3. Hostinger auto-detects **Astro**. Confirm these settings:
   - **Build command:** `npm run build`
   - **Start command:** `npm run start`
     *(this runs `node dist/server/entry.mjs` — see `package.json`)*
   - **Application Startup File:** **`dist/server/entry.mjs`**
     *(this is the file Astro's Node adapter generates on every build, and
     the exact path Hostinger's Astro preset expects. It does not exist
     until the first build runs — that's expected: set the field to this
     value regardless, let the build run, then restart. `package.json`'s
     `main` and `start` script both point here too, so all three agree.)*
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
   | `HOST` *(optional)* | `0.0.0.0` |

   > `HOST` is optional — the app already binds to `0.0.0.0` by default (set
   > in `astro.config.mjs`), so the platform's proxy can always reach it.
   > Only add this variable if you ever see an "unreachable"/502 symptom
   > despite a successful build. `PORT` is provided by Hostinger
   > automatically — do **not** set it yourself.
   >
   > `SITE_URL` only affects canonical/SEO URLs and is read at **build
   > time** (it sets Astro's `site`). Set it before the build runs so it
   > takes effect; if it's missing or malformed the build still succeeds
   > and falls back to the default domain — it can never break the build.
   > The other 7 variables are read at **runtime** on every request.

5. Click **Deploy** and wait for the build to finish. On first start the app
   creates the database tables and loads the starter content automatically.

> **Why this build is hardened against common Hostinger failures:**
> - `sharp` (image tool used only by the local favicon script) is an
>   `optionalDependency`, so even if its native Linux binary can't be
>   fetched during `npm install`, the install and build still succeed —
>   it's never needed at build or runtime (favicons are pre-generated in
>   `public/icons/`).
> - The build needs **no database and no environment variables** — pages
>   are only rendered at request time, never during the build, so a build
>   can't fail on a missing DB or unset variable.
> - The build succeeds even with a **production-only** install
>   (`npm install --omit=dev`); no devDependency is required to build.
> - No unused dependencies with strict Node-version requirements remain, so
>   there are **no engine-mismatch warnings**.

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

## ✅ Do / ❌ Don't

**Do:**
- ✅ Use the exact values from **Databases → MySQL** for `DB_HOST/NAME/USER/PASSWORD` — copy-paste them, don't retype.
- ✅ Set `DB_HOST=localhost` (Hostinger's internal MySQL is not reachable via any external hostname on shared/Business plans).
- ✅ Pick Node **20 or 22** in the app's Node.js version dropdown — Astro 4 requires Node ≥ 18.20, and the adapter used here is tested on 20/22.
- ✅ Generate `ADMIN_SECRET` fresh with the PowerShell/openssl command in Step 2 — don't reuse a password or a short string.
- ✅ Wait for the **first deploy** to finish fully before opening `/admin` — table creation happens on the first request and can take a few seconds.
- ✅ Re-deploy (or restart the app) after changing **any** environment variable — Node reads them once at startup, so hPanel's save alone does not apply them until restart.
- ✅ Keep `.env` **out** of anything you upload — it's already excluded from the zip; production config lives only in hPanel's Environment Variables screen.
- ✅ Back up the database regularly (see Backups section below) — this is your only copy of blog posts, prices, and images.

**Don't:**
- ❌ Don't set `DB_HOST` to your domain name or a public IP — always `localhost` on Hostinger Business.
- ❌ Don't skip the **Node.js version** selection — leaving it on an old default (e.g. 16) will fail the build with a cryptic engine error.
- ❌ Don't hand-edit files inside `seed/` expecting it to update the live site — that folder is only read once, when the database is completely empty. Edit content in `/admin` instead.
- ❌ Don't commit or upload a `.env` file with real secrets — set them in hPanel's environment variables UI only.
- ❌ Don't use a short/weak `ADMIN_PASSWORD` — this single password protects your entire content database.
- ❌ Don't forget to enable **Force HTTPS** — mixed content (favicons/images loading over HTTP) will show a browser warning.
- ❌ Don't delete the MySQL database from hPanel while the app is still pointed at it — the site will go down immediately (500 errors) until it's recreated and env vars updated.
- ❌ Don't run two Node apps against the **same** database with different `DB_NAME` typos — a small typo silently creates a second, empty, database-shaped app that looks broken.

---

## Common errors & how to fix them

### Build warning: "engine ... requires Node >=X.Y.Z" (e.g. a `sitemap` package)
- **Cause:** a leftover/unused dependency somewhere in the tree declares a
  narrower Node engine requirement than your hosting environment's exact
  patch version (e.g. it wants `20.19.5` but the host runs `20.19.4`).
- **Fix:** this build no longer carries that dependency — `@astrojs/sitemap`
  was removed because the site already generates its own `/sitemap.xml`
  directly (see `src/pages/sitemap.xml.ts`) and never needed the package.
  If you see a similar warning again after adding a new dependency, check
  whether it's actually used (`grep -r "package-name" src/` and
  `astro.config.mjs`) before assuming you need to match Hostinger's exact
  Node patch version — removing an unused dependency is usually simpler and
  more reliable than waiting for the host to update.

### Build succeeds, but the deployment is marked failed / the app is "unreachable" / 502 with no obvious error
- **Most common cause — the server was binding to `localhost` only.** A Node
  app that listens on `127.0.0.1` works when tested from inside the server,
  but the hosting platform reaches your app through a proxy from *outside*
  that loopback interface — so it can't connect, and marks the deployment
  failed even though the build succeeded and the process is running (the
  logs show no error because the app itself didn't crash).
  **This is already fixed in this project:** `astro.config.mjs` sets
  `server: { host: true }`, so the built server binds to `0.0.0.0` (all
  interfaces) and the platform's proxy can reach it. You don't need to do
  anything — but if you ever see this symptom, confirm that setting is still
  present, and as a belt-and-suspenders measure you can also add a
  `HOST=0.0.0.0` environment variable (the platform's own `PORT`/`HOST`
  values, if it sets them, always take precedence at runtime).

### Build logs show success, but the app still won't serve requests / "entry file not found" at runtime
- **Cause 1 — Startup File / start command doesn't match the built path.**
  The Application Startup File must be **`dist/server/entry.mjs`** — that's
  what Astro's Node adapter generates and what Hostinger's Astro preset
  expects. This project is already configured so all three agree:
  `package.json` has `"main": "dist/server/entry.mjs"` and its `start`
  script runs `node dist/server/entry.mjs`. If the panel's Startup File
  field was ever left on a stale value (a typo, or a default from before
  framework re-detection), Hostinger will flag the entry file as "not
  found" even though the build succeeded. **Fix:** set Application Startup
  File to exactly `dist/server/entry.mjs`, save, then redeploy/restart.
- **Cause 2 — the app was never restarted after the build finished.** A
  successful build does not always automatically restart the running
  process. **Fix:** after any build (or after changing environment
  variables, the startup file, or the Node version), always click
  **Restart** explicitly and wait ~15–20 seconds before testing again.
- **Cause 3 — the Application Root changed after a build already ran.**
  This app's compiled server bakes the absolute path to its own static
  assets at build time. If you change the Application Root folder (rename
  it, move it, or re-point the app at a different path) *after* a build
  has already produced a `dist/` folder, that old `dist/` now points at a
  path that no longer matches — pages may load but CSS/JS/images can 404.
  **Fix:** run the **Build** step again after any Application Root change,
  so the paths get baked correctly for the new location.
- **Never upload a `dist/` folder built on your own computer.** For the
  same reason as Cause 3 — the build output embeds the exact filesystem
  path of the machine and folder it was built on/in. A `dist/` built on
  your PC will contain a path like `C:/Users/you/...` or
  `/home/you/project/...` that does not exist on Hostinger's server, and
  static assets will silently fail to load even though the page itself
  renders. Always let **Hostinger's own Build step** produce `dist/` —
  never build it locally and zip/upload it as a shortcut.

### "Application failed to start" / the app won't boot
- **Cause:** usually a missing or wrong environment variable, or the wrong start command.
- **Fix:** In hPanel → your app → **Logs**, look for the first error line (usually near the top of the crash). Confirm:
  - Start command is exactly `npm run start`
  - Build command is exactly `npm run build`
  - All 9 environment variables from Step 3 are present (no typos in the *names*, not just the values)
  - Re-deploy after fixing.

### `ER_ACCESS_DENIED_ERROR` / "Access denied for user ... to database"
- **Cause:** `DB_USER` / `DB_PASSWORD` don't match what hPanel created, or the user isn't assigned to that database.
- **Fix:** hPanel → Databases → MySQL Databases → confirm the username is attached to the database (Hostinger sometimes requires an explicit "Add user to database" step). Re-copy the password (special characters sometimes get lost during manual retyping — copy-paste it).

### `ECONNREFUSED 127.0.0.1:3306` or `ETIMEDOUT` connecting to MySQL
- **Cause:** `DB_HOST` is wrong, or the MySQL service isn't in the same hosting account/plan.
- **Fix:** Confirm `DB_HOST=localhost` and `DB_PORT=3306`. If you're on a plan where MySQL runs on a separate internal host, hPanel's database page will show the correct host — use that value instead.

### 502 Bad Gateway / 503 Service Unavailable right after deploy
- **Cause:** the app is still starting (creating tables + seeding on first boot can take 10–20 seconds), or it crashed.
- **Fix:** Wait 30 seconds and refresh. If it persists, check **Logs** for a stack trace — most commonly a missing environment variable (see above).

### Build fails with "Node engine mismatch" or `EBADENGINE`
- **Cause:** the app's Node.js version is set below 20.
- **Fix:** hPanel → your app → **Settings** → change Node.js version to `20` or `22`, then redeploy.

### Build fails with `sharp` install errors
- **Cause:** `sharp` (used only by the local `npm run setup` favicon script) sometimes fails to install prebuilt binaries on constrained build environments.
- **Fix:** This does not affect the live site — `sharp` is a devDependency used only to *regenerate* favicons locally, not at runtime. If the Hostinger build step errors on it, you can ignore/remove it from `devDependencies` before uploading (favicons are already generated and included in `public/icons/`).

### `/admin` shows "ADMIN_SECRET environment variable is not set"
- **Cause:** `ADMIN_SECRET` (or `ADMIN_PASSWORD`) is missing from the app's environment variables, or the app wasn't restarted after adding it.
- **Fix:** Add the variable in hPanel, then **restart/redeploy** the app — env vars only take effect on process start.

### Logging in to `/admin` always says "Incorrect password"
- **Cause:** `ADMIN_PASSWORD` in hPanel doesn't match what you're typing, or there's a trailing space copied into the env var field.
- **Fix:** Re-enter the value in hPanel carefully (avoid pasting extra whitespace/newlines), restart the app, try again. If you're locked out after 8 attempts, wait 15 minutes (rate limit) or restart the app to reset the in-memory counter.

### Image upload fails or hangs on a large photo
- **Cause:** the built-in cap is 5 MB per image (`admin-forms.ts`); anything larger is rejected with a message, not a silent failure.
- **Fix:** Compress the photo (any online image compressor, or resize to ~1600px wide) and re-upload.

### Uploaded images / blog photos don't show up
- **Cause:** rare, but can happen if `DB_NAME` was changed after some images were already uploaded to a *different* database.
- **Fix:** Images are stored in the `images` table of whichever database the app is currently pointed at — make sure `DB_NAME` hasn't changed since the images were uploaded. If it has, restore the correct `DB_NAME` value.

### Domain shows Hostinger's default "It works!" page instead of the site
- **Cause:** DNS hasn't propagated yet, or the domain wasn't assigned to the Node.js app (it may still be pointed at the default Apache/LiteSpeed site).
- **Fix:** hPanel → your Node.js app → **Domain** → confirm the domain is assigned there specifically, not just added to the account. DNS propagation can take up to 24–48 hours after nameserver changes (usually much faster in practice).

### Favicon doesn't update after replacing the logo
- **Cause:** browsers cache favicons aggressively.
- **Fix:** Hard-refresh (`Ctrl+F5`), or open the site in a private/incognito window to confirm the new icon actually deployed.

### Contact form submissions aren't appearing in "Enquiries"
- **Cause:** almost always a false alarm — check you're looking at `/admin/messages` (not old Netlify Forms, which this build no longer uses at all).
- **Fix:** Submit a real test enquiry from `/contact` and check `/admin/messages` immediately after; it should appear at the top instantly (no delay, no external service involved).

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
