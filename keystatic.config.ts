import { config, fields, collection, singleton } from '@keystatic/core';

// For local development, 'local' storage writes directly to your filesystem.
// For production (Netlify), set KEYSTATIC_GITHUB_CLIENT_ID, KEYSTATIC_GITHUB_CLIENT_SECRET,
// and KEYSTATIC_SECRET in your Netlify environment variables, and fill in your actual
// GitHub username/org and repo name below.
const isProd = process.env.NODE_ENV === 'production';

export default config({
  storage: isProd
    ? {
        kind: 'github',
        repo: {
          owner: process.env.GITHUB_REPO_OWNER ?? 'your-github-username',
          name: process.env.GITHUB_REPO_NAME ?? 'omcomputers-web',
        },
      }
    : { kind: 'local' },

  ui: {
    brand: { name: 'OM Computers Admin' },
  },

  collections: {
    // ── BLOG ──────────────────────────────────────────────────────────
    blog: collection({
      label: 'Blog Posts',
      slugField: 'title',
      path: 'src/content/blog/*',
      format: { contentField: 'body' },
      entryLayout: 'content',
      schema: {
        title: fields.text({ label: 'Post Title' }),
        published: fields.checkbox({
          label: 'Published',
          description: 'Uncheck to keep as draft. Drafts never appear on the site.',
          defaultValue: false,
        }),
        publishDate: fields.date({
          label: 'Publish Date',
          defaultValue: { kind: 'today' },
        }),
        excerpt: fields.text({
          label: 'Excerpt',
          description: 'Shown on blog listing cards. Max 160 characters.',
          multiline: true,
          validation: { length: { max: 160 } },
        }),
        tags: fields.array(
          fields.text({ label: 'Tag' }),
          {
            label: 'Tags',
            description: 'e.g. CCTV, Repair, Tips, Biometric',
            itemLabel: props => props.value ?? 'Tag',
          }
        ),
        coverImage: fields.image({
          label: 'Cover Image',
          description: '16:9 ratio recommended. Shown at the top of the post and on blog cards.',
          directory: 'public/uploads/blog',
          publicPath: '/uploads/blog',
        }),
        body: fields.mdx({
          label: 'Post Body',
          options: {
            heading: [1, 2, 3, 4],
            bold: true,
            italic: true,
            strikethrough: true,
            code: true,
            codeBlock: true,
            blockquote: true,
            link: true,
            orderedList: true,
            unorderedList: true,
            table: true,
            divider: true,
            image: {
              directory: 'public/uploads/blog/inline',
              publicPath: '/uploads/blog/inline',
              schema: {
                alt: fields.text({
                  label: 'Alt text',
                  description: 'Describe the image for screen readers',
                }),
                caption: fields.text({
                  label: 'Caption (optional)',
                }),
              },
            },
          },
        }),
      },
    }),

    // ── PRICE LIST ────────────────────────────────────────────────────
    prices: collection({
      label: 'Price List Categories',
      slugField: 'category',
      path: 'src/content/prices/*',
      format: { data: 'json' },
      schema: {
        category: fields.text({
          label: 'Category Name',
          description: 'e.g. Processors, Laptops, RAM, Storage, Accessories',
        }),
        items: fields.array(
          fields.object({
            name: fields.text({
              label: 'Product Name',
              description: 'e.g. Intel Core i5-13400F',
            }),
            brand: fields.text({
              label: 'Brand',
              description: 'e.g. Intel, Kingston, Seagate',
            }),
            specs: fields.text({
              label: 'Specs',
              multiline: true,
              description: 'e.g. 10-Core, 2.5 GHz base, LGA1700 socket',
            }),
            price: fields.number({
              label: 'Price (INR)',
              validation: { min: 0 },
            }),
            inStock: fields.checkbox({ label: 'In Stock', defaultValue: true }),
            image: fields.image({
              label: 'Product Image',
              description: 'Square image recommended. Displayed at 150x150px.',
              directory: 'public/uploads/prices',
              publicPath: '/uploads/prices',
            }),
          }),
          {
            label: 'Products',
            itemLabel: props => props.fields.name.value ?? 'Product',
          }
        ),
      },
    }),
  },

  // ── SITE SETTINGS SINGLETON ─────────────────────────────────────────
  singletons: {
    siteSettings: singleton({
      label: 'Site Settings',
      path: 'src/content/settings/site',
      format: { data: 'json' },
      schema: {
        phone: fields.text({ label: 'Phone Number' }),
        whatsapp: fields.text({
          label: 'WhatsApp Number',
          description: 'With country code, no spaces. e.g. 919876543210',
        }),
        email: fields.text({ label: 'Email Address' }),
        address: fields.text({ label: 'Full Address', multiline: true }),
        googleMaps: fields.url({ label: 'Google Maps Embed URL' }),
        openHours: fields.text({
          label: 'Opening Hours',
          description: 'e.g. Monday to Saturday, 9 AM to 7 PM',
        }),
      },
    }),
  },
});
