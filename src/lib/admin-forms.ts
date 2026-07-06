import type { PostInput } from './content';
import { saveImage } from './content';
import { slugify } from './slug';

export const MAX_UPLOAD_BYTES = 5 * 1024 * 1024; // 5 MB per image
export const MAX_REQUEST_BYTES = 12 * 1024 * 1024;

const IMAGE_MIMES = new Set(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml', 'image/avif']);

export function requestTooLarge(request: Request): boolean {
  const len = Number(request.headers.get('content-length') ?? 0);
  return len > MAX_REQUEST_BYTES;
}

/** Stores an uploaded image in the DB, returns its public URL (or null if empty). */
export async function storeUpload(file: unknown): Promise<{ url: string | null; error?: string }> {
  if (!(file instanceof File) || file.size === 0) return { url: null };
  if (file.size > MAX_UPLOAD_BYTES) {
    return { url: null, error: 'Image is too large — maximum 5 MB. Please compress it and try again.' };
  }
  if (!IMAGE_MIMES.has(file.type)) {
    return { url: null, error: 'Unsupported file type — please upload a JPG, PNG, WebP, or GIF image.' };
  }
  const data = Buffer.from(await file.arrayBuffer());
  const id = await saveImage(file.name ?? 'upload', file.type, data);
  return { url: `/api/images/${id}` };
}

export async function parsePostForm(
  form: FormData,
  currentCover: string | null,
): Promise<{ input?: PostInput; error?: string }> {
  const title = String(form.get('title') ?? '').trim();
  if (!title) return { error: 'Title is required.' };

  const rawSlug = String(form.get('slug') ?? '').trim();
  const slug = slugify(rawSlug || title);

  const excerpt = String(form.get('excerpt') ?? '').trim().slice(0, 500);
  const tags = String(form.get('tags') ?? '')
    .split(',')
    .map(t => t.trim())
    .filter(Boolean);

  const rawDate = String(form.get('publishDate') ?? '');
  const publishDate = /^\d{4}-\d{2}-\d{2}$/.test(rawDate)
    ? rawDate
    : new Date().toISOString().slice(0, 10);

  let coverImage = currentCover;
  if (form.get('removeCover') === 'on') coverImage = null;
  const upload = await storeUpload(form.get('cover'));
  if (upload.error) return { error: upload.error };
  if (upload.url) coverImage = upload.url;

  return {
    input: {
      slug,
      title,
      excerpt,
      tags,
      coverImage,
      body: String(form.get('body') ?? ''),
      published: form.get('published') === 'on',
      publishDate,
    },
  };
}
