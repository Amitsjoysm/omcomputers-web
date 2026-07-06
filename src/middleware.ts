import { defineMiddleware } from 'astro:middleware';
import { SESSION_COOKIE, verifySessionToken } from './lib/auth';

// Every /admin page except the login screen requires a valid session.
export const onRequest = defineMiddleware((context, next) => {
  const { pathname } = context.url;
  if (!pathname.startsWith('/admin')) return next();
  if (pathname === '/admin/login' || pathname === '/admin/login/') return next();

  const token = context.cookies.get(SESSION_COOKIE)?.value;
  if (!verifySessionToken(token)) {
    return context.redirect('/admin/login');
  }
  return next();
});
