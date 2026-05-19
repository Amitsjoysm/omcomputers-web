import { defineCollection, z } from 'astro:content';

const blog = defineCollection({
  type: 'content',
  schema: ({ image }) => z.object({
    title:       z.string(),
    excerpt:     z.string().max(160),
    tags:        z.array(z.string()),
    coverImage:  image().optional(),
    published:   z.boolean().default(false),
    publishDate: z.coerce.date(),
  }),
});

const prices = defineCollection({
  type: 'data',
  schema: ({ image }) => z.object({
    category: z.string(),
    items: z.array(z.object({
      name:    z.string(),
      brand:   z.string(),
      specs:   z.string(),
      price:   z.number(),
      image:   image().optional(),
      inStock: z.boolean().default(true),
    })),
  }),
});

export const collections = { blog, prices };
