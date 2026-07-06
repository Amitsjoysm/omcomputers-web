import { marked } from 'marked';

marked.setOptions({ gfm: true, breaks: false });

/**
 * Render trusted (admin-authored) markdown to HTML.
 * Content is written only by the authenticated site owner in /admin.
 */
export function renderMarkdown(md: string): string {
  return marked.parse(md ?? '', { async: false }) as string;
}
