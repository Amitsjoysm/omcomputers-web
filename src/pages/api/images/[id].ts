import type { APIRoute } from 'astro';
import { getImage } from '../../../lib/content';

export const GET: APIRoute = async ({ params }) => {
  const id = Number(params.id);
  if (!Number.isInteger(id) || id <= 0) {
    return new Response('Not found', { status: 404 });
  }
  const image = await getImage(id);
  if (!image) return new Response('Not found', { status: 404 });

  return new Response(new Uint8Array(image.data), {
    headers: {
      'Content-Type': image.mime,
      // URLs are immutable (a new upload gets a new id), so cache hard.
      'Cache-Control': 'public, max-age=31536000, immutable',
    },
  });
};
