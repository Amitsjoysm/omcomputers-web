#!/usr/bin/env node
/**
 * Production server entry — Passenger-compatible and portable.
 *
 * Astro is built in MIDDLEWARE mode, so ./dist/server/entry.mjs exports a
 * request `handler` instead of starting its own server. This file:
 *   1. serves static assets from ./dist/client (resolved RELATIVE to this
 *      file, so the built app can be uploaded and run from any directory),
 *   2. delegates everything else to Astro's SSR handler,
 *   3. listens on the port OR Unix-socket path the host provides.
 *
 * Works on:
 *   - Hostinger shared hosting (Phusion Passenger passes a socket path in
 *     process.env.PORT — handled below),
 *   - container / proxy platforms (numeric PORT, binds 0.0.0.0),
 *   - a plain `node server.mjs` locally.
 *
 * Point your host's "Application startup file" at this file (server.mjs).
 */
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { handler as ssrHandler } from './dist/server/entry.mjs';

// Absolute path to the built static assets, WITHOUT a trailing slash so the
// path-traversal guard below compares cleanly.
const clientDir = fileURLToPath(new URL('./dist/client/', import.meta.url)).replace(/[\\/]+$/, '');

const MIME = {
  '.html': 'text/html; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.mjs': 'text/javascript; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.webmanifest': 'application/manifest+json; charset=utf-8',
  '.xml': 'application/xml; charset=utf-8',
  '.txt': 'text/plain; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif': 'image/gif',
  '.webp': 'image/webp',
  '.avif': 'image/avif',
  '.ico': 'image/x-icon',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf',
  '.map': 'application/json; charset=utf-8',
};

/** Serve a static file from dist/client if one matches. Returns true if handled. */
function serveStatic(req, res) {
  if (req.method !== 'GET' && req.method !== 'HEAD') return false;
  let pathname;
  try {
    pathname = decodeURIComponent(new URL(req.url, 'http://localhost').pathname);
  } catch {
    return false;
  }
  if (pathname.endsWith('/')) return false; // let SSR handle directory/index routes

  // Resolve within clientDir and block path traversal.
  const filePath = path.join(clientDir, path.normalize(pathname));
  if (filePath !== clientDir && !filePath.startsWith(clientDir + path.sep)) return false;

  let stat;
  try {
    stat = fs.statSync(filePath);
  } catch {
    return false;
  }
  if (!stat.isFile()) return false;

  const ext = path.extname(filePath).toLowerCase();
  res.setHeader('Content-Type', MIME[ext] || 'application/octet-stream');
  // Astro's hashed build assets are immutable; cache them hard.
  if (pathname.startsWith('/_astro/')) {
    res.setHeader('Cache-Control', 'public, max-age=31536000, immutable');
  }
  res.setHeader('Content-Length', stat.size);
  if (req.method === 'HEAD') {
    res.end();
    return true;
  }
  fs.createReadStream(filePath).pipe(res);
  return true;
}

const server = http.createServer((req, res) => {
  try {
    if (serveStatic(req, res)) return;
  } catch (err) {
    // fall through to SSR on any static-serving error
  }
  ssrHandler(req, res, (err) => {
    if (err) {
      console.error(err);
      res.statusCode = 500;
      res.end('Internal Server Error');
    } else {
      res.statusCode = 404;
      res.end('Not Found');
    }
  });
});

// Passenger passes a Unix socket PATH in PORT; container hosts pass a NUMBER.
const rawPort = process.env.PORT || '3000';
const asNumber = Number(rawPort);
const isNumericPort = Number.isInteger(asNumber) && String(asNumber) === String(rawPort).trim();

function onListening() {
  const addr = server.address();
  const where = typeof addr === 'string' ? addr : `${addr?.address}:${addr?.port}`;
  console.log(`[server] OM Computers listening on ${where}`);
}

if (isNumericPort) {
  server.listen(asNumber, process.env.HOST || '0.0.0.0', onListening);
} else {
  // Unix socket path (Passenger). Remove any stale socket first.
  try { fs.unlinkSync(rawPort); } catch { /* ignore */ }
  server.listen(rawPort, onListening);
}

// Keep the process alive and log fatal errors instead of dying silently.
process.on('uncaughtException', (err) => console.error('[uncaughtException]', err));
process.on('unhandledRejection', (err) => console.error('[unhandledRejection]', err));
