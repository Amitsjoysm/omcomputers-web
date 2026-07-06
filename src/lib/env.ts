// Runtime env lookup. On Hostinger the values come from the app's
// Environment variables (process.env). In local dev they come from .env,
// which Astro exposes on import.meta.env. process.env wins so the built
// server always honours the host's configuration.
export function env(name: string, fallback = ''): string {
  const fromProcess = process.env[name];
  if (fromProcess !== undefined && fromProcess !== '') return fromProcess;
  const fromVite = (import.meta.env as Record<string, unknown>)?.[name];
  if (typeof fromVite === 'string' && fromVite !== '') return fromVite;
  return fallback;
}
