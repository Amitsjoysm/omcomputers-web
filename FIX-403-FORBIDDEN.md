# Fixing "Forbidden" (403) after deploying to Hostinger

A **403 Forbidden** right after uploading to `public_html` and setting up the
Node.js app is almost always one of these five causes, in order of how often
they actually happen. Work through them top to bottom — #1 fixes it 90% of
the time.

---

## 1. The zip extracted into a nested folder (most common cause)

**Symptom:** you uploaded a zip, extracted it in File Manager, and now
`public_html` contains a folder like `public_html/omcomputers-web/` — with
`package.json` *inside* that folder, not directly in `public_html`.

**Why this breaks it:** Hostinger's Node.js app looks for `package.json` at
the **Application Root** you configured. If the real files are one folder
deeper than that root, Hostinger can't find or start your app at all — and
Apache/LiteSpeed falls back to trying to list the directory. Since directory
listing is disabled, that fallback is a **403 Forbidden**.

**Fix — use the flat zip:**
- Use `omcomputers-web-public-html.zip` from this package — it has
  `package.json`, `src/`, `public/`, etc. **directly at the top level of the
  zip**, with no wrapper folder.
- In hPanel File Manager: upload it into `public_html`, right-click →
  **Extract**, and choose to extract **into the current folder** (not into a
  new subfolder). Afterwards `public_html/package.json` should exist
  directly — confirm this before moving on.
- If you already extracted the old zip into a subfolder, either re-extract
  the flat zip properly, or set the Node.js app's **Application Root** to
  that subfolder (e.g. `public_html/omcomputers-web`) instead of
  `public_html`.

---

## 2. Leftover static files fighting with the Node.js app

**Symptom:** this domain previously hosted a static site (or a default
Hostinger placeholder page), and `public_html` still has an old
`index.html`, `.htaccess`, or `default.php` sitting next to your uploaded
files.

**Why this breaks it:** Hostinger's Node.js app setup writes its own
`.htaccess`/Passenger routing rules into the app root. An old `.htaccess`
left behind (with `Deny from all`, or rules pointing at a different app) can
override or conflict with it, producing 403.

**Fix:**
- In File Manager, show hidden files (⋮ menu → **Show Hidden Files**) and
  check `public_html` for any `.htaccess` **not** created by this deploy.
  Delete or rename it, then restart the Node.js app.
- Delete any leftover `index.html` from a previous static site.

---

## 3. The app was never actually started/installed

**Symptom:** files are in the right place, but you never clicked through
hPanel's Node.js app **Install** / **Build** / **Restart** buttons after
uploading — you just uploaded files and expected Apache to serve them
directly.

**Why this breaks it:** unlike a static site, a Node.js app is not served by
Apache reading files off disk — Hostinger runs your app as a background
Node process and proxies requests to it. If that process was never started
(npm install never ran, or the app was never restarted after files
changed), there's nothing listening, and the request falls through to a
generic 403.

**Fix — in hPanel → Websites → your domain → Node.js:**
1. Confirm **Application Root** points at the folder containing
   `package.json` (see #1).
2. Confirm **Application Startup File** is set to **`dist/server/entry.mjs`**
   — this is what Hostinger's own Astro platform preset expects (see
   `HOSTINGER-DEPLOYMENT.md` for the full explanation, including the
   `server.mjs` fallback that exists for other panels).
3. Click **NPM Install** (installs dependencies).
4. Run the **Build** command: `npm run build`.
5. Click **Restart** on the app.
6. Wait ~15–20 seconds, then reload the site.

---

## 4. File permissions from the zip upload

**Symptom:** everything above looks correct, but it's still 403.

**Why this breaks it:** zip uploads through File Manager occasionally
extract files with permissions Apache/Passenger can't read (e.g. `600`
instead of `644` for files, or `700` instead of `755` for folders).

**Fix:**
- In File Manager, select `public_html` → right-click → **Change
  Permissions** → apply **755** to folders and **644** to files
  (recursively, if the tool offers it).
- Restart the Node.js app afterward.

---

## 5. Wrong Application URL / domain mapping

**Symptom:** the app works at a Hostinger-provided preview URL but not at
your real domain (or vice versa), or you see 403 specifically on
`https://yourdomain.com` but the IP/preview URL is fine.

**Fix:**
- hPanel → your Node.js app → **Domain** — confirm your actual domain (and
  `www.`) is assigned to *this* app, not left pointed at the default
  Apache site.
- If DNS was changed recently, allow time to propagate before assuming the
  app itself is broken.

---

## Still stuck? Read the actual error

A generic Apache "Forbidden" page only tells you *that* something failed —
not *why*. The real reason is in the app's own logs:

**hPanel → your Node.js app → Logs** (sometimes labeled **Application
Logs** or accessible via **Advanced → Errors**). Look for the first error
line — it will usually say something like `Cannot find module`,
`ECONNREFUSED`, or `Error: Cannot find package.json`, which points straight
at the real cause (usually #1 or #3 above).

If the log shows a **database connection error** instead (not a 403 at
all, but easy to confuse), see the "Common errors" section in
`HOSTINGER-DEPLOYMENT.md` — that's a different issue (MySQL credentials),
not this Forbidden error.

---

## Quick checklist before asking for more help

- [ ] `public_html/package.json` exists directly (not one folder deeper)
- [ ] No leftover `.htaccess` or `index.html` from a previous static site
- [ ] Application Startup File = `dist/server/entry.mjs`
- [ ] Clicked **NPM Install**, ran **Build**, then **Restart**
- [ ] Folder permissions 755, file permissions 644
- [ ] Domain is assigned to this specific Node.js app in hPanel
- [ ] Checked the app's **Logs** for the real underlying error
