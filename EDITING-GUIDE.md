# How to Edit the OM Computers Website

You have THREE ways — pick whichever feels easiest.

---

## Option A — Admin Panel (easiest, no code needed)

1. Go to https://omcomputers.co/keystatic
2. Log in with your GitHub account (you must be added as a repo collaborator)
3. Use the menu:
   - **Blog Posts** — write or edit posts, upload images, toggle published
   - **Price List Categories** — add or edit products, prices, and images
   - **Site Settings** — update phone number, email, address, opening hours
4. Click Save — the site rebuilds automatically in about 1 minute on Netlify

---

## Option B — Edit directly on GitHub (for quick text changes)

1. Go to https://github.com/[your-org]/omcomputers-web
2. Navigate to the file you want to change:
   - Blog posts: `src/content/blog/` — each post is a `.mdx` file
   - Prices:     `src/content/prices/` — each category is a `.json` file
   - Contact/phone/email: `src/content/settings/site.json`
3. Click the file name, then the ✏️ pencil (Edit) icon
4. Make your changes
5. Scroll down → click **Commit changes** → the site rebuilds automatically

### What the files look like

**Blog post** (`.mdx` file):
The block between `---` lines at the top is settings. Change the text values only.
Below that is the article in Markdown — simple formatting rules:
  - `**bold text**` → **bold**
  - `*italic text*` → *italic*
  - `## Sub Heading` → a subheading
  - `- item` → a bullet point
  - `![alt text](/uploads/blog/image.jpg)` → an image

**Price item** (`.json` file):
Find the product by name, then change the number after `"price":`.
Do not change the curly braces, square brackets, or commas — only the values.

---

## Option C — Local editing with VS Code

1. Install VS Code (https://code.visualstudio.com/) and Node.js 20+
2. Clone the repo: `git clone https://github.com/[your-org]/omcomputers-web`
3. Install packages: `npm install`
4. Start preview: `npm run dev` — opens at http://localhost:4321
5. Edit any `.mdx` or `.json` file in `src/content/` and see changes live
6. When done: `git add . && git commit -m "update" && git push`
   The site rebuilds on Netlify automatically

---

## What NOT to edit (unless you know what you're doing)

- `src/components/` — Astro component files (site structure and design)
- `src/styles/global.css` — the design tokens and CSS
- `astro.config.mjs` or `keystatic.config.ts` — build and CMS config
- Any file ending in `.ts` or `.tsx` — TypeScript code
