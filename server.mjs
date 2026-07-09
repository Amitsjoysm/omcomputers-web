#!/usr/bin/env node
// Fallback startup entrypoint for generic panel-based Node.js hosts
// (cPanel/Plesk "Node.js App" setups) that ask you to pick an
// "Application Startup File" before the project has ever been built —
// when dist/server/entry.mjs (the real Astro-built server) doesn't exist
// yet, so a path under dist/ can't be selected or typed reliably.
//
// NOT used on Hostinger: Hostinger's Node.js hosting has an Astro-specific
// preset that expects the Application Startup File to be
// dist/server/entry.mjs directly — point it there instead, and ignore
// this file entirely. See HOSTINGER-DEPLOYMENT.md for details.
import './dist/server/entry.mjs';
