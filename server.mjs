#!/usr/bin/env node
// Root-level startup entrypoint for panel-based Node.js hosts (Hostinger, cPanel, Plesk).
//
// Why this file exists: the real Astro-built server lives at
// dist/server/entry.mjs, but that path only exists *after* a build runs.
// Many hosting panels ask you to pick an "Application Startup File" at the
// time you first create the app — before any build has happened — so a
// path under dist/ isn't selectable yet and has to be typed by hand, which
// is easy to get wrong or leave stale. This file sits at the project root
// so it's always present and discoverable, and simply boots the real
// server once it exists.
//
// Point your host's "Application Startup File" at this file (server.mjs).
import './dist/server/entry.mjs';
