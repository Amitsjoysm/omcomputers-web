import { useState } from 'react';

interface NavLink {
  label: string;
  href: string;
  hasDropdown?: boolean;
}

interface Service {
  icon: string;
  label: string;
  sub: string;
  href: string;
}

interface Props {
  links: NavLink[];
  services: Service[];
  phone: string;
}

export default function MobileNav({ links, services, phone }: Props) {
  const [open, setOpen] = useState(false);
  const [servicesOpen, setServicesOpen] = useState(false);

  const close = () => { setOpen(false); setServicesOpen(false); };

  return (
    <>
      {/* Hamburger */}
      <button
        aria-label={open ? 'Close menu' : 'Open menu'}
        aria-expanded={open}
        aria-controls="mobile-drawer"
        onClick={() => setOpen(!open)}
        style={{
          background: 'none', border: 'none', cursor: 'pointer',
          padding: '8px', alignItems: 'center',
          borderRadius: 'var(--radius-sm)',
          transition: 'background 150ms',
        }}
        className="nav-hamburger"
      >
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round">
          {open
            ? <><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></>
            : <><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></>
          }
        </svg>
      </button>

      {/* Backdrop */}
      {open && (
        <div
          onClick={close}
          aria-hidden="true"
          style={{
            position: 'fixed', inset: 0, zIndex: 199,
            background: 'rgba(13,27,42,.45)',
            backdropFilter: 'blur(3px)',
          }}
        />
      )}

      {/* Drawer */}
      <div
        id="mobile-drawer"
        role="dialog"
        aria-modal="true"
        aria-label="Navigation menu"
        style={{
          position: 'fixed',
          top: 0, right: 0, bottom: 0,
          width: 'min(320px, 90vw)',
          zIndex: 200,
          background: 'var(--surface)',
          boxShadow: '-4px 0 32px rgba(0,0,0,.12)',
          display: 'flex',
          flexDirection: 'column',
          transform: open ? 'translateX(0)' : 'translateX(100%)',
          transition: 'transform 280ms cubic-bezier(.4,0,.2,1)',
          overflowY: 'auto',
        }}
      >
        {/* Drawer header */}
        <div style={{
          display: 'flex', alignItems: 'center', justifyContent: 'space-between',
          padding: '16px 20px',
          borderBottom: '1px solid var(--border)',
          position: 'sticky', top: 0,
          background: 'var(--surface)',
          zIndex: 1,
        }}>
          <img src="/logo.png" alt="OM Computers" style={{ height: '44px', width: 'auto', objectFit: 'contain' }} />
          <button
            onClick={close}
            aria-label="Close menu"
            style={{
              background: 'var(--surface-2)', border: 'none', cursor: 'pointer',
              padding: '8px', borderRadius: 'var(--radius-sm)',
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              transition: 'background 150ms',
            }}
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        {/* Nav items */}
        <nav style={{ padding: '8px 12px', flex: 1 }}>
          <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
            {links.map(link => (
              <li key={link.href}>
                {link.hasDropdown ? (
                  <div>
                    {/* Services accordion trigger */}
                    <button
                      onClick={() => setServicesOpen(!servicesOpen)}
                      aria-expanded={servicesOpen}
                      style={{
                        width: '100%', display: 'flex', alignItems: 'center',
                        justifyContent: 'space-between',
                        padding: '12px 16px',
                        background: servicesOpen ? 'var(--primary-light)' : 'none',
                        border: 'none', cursor: 'pointer',
                        borderRadius: 'var(--radius-md)',
                        fontFamily: 'var(--font-display)',
                        fontSize: '15px', fontWeight: 700,
                        color: servicesOpen ? 'var(--primary)' : 'var(--text-primary)',
                        transition: 'background 150ms, color 150ms',
                      }}
                    >
                      {link.label}
                      <svg
                        width="16" height="16" viewBox="0 0 20 20" fill="currentColor"
                        style={{ transition: 'transform 220ms', transform: servicesOpen ? 'rotate(180deg)' : 'rotate(0deg)', flexShrink: 0 }}
                      >
                        <path fillRule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clipRule="evenodd" />
                      </svg>
                    </button>

                    {/* Services list */}
                    <div style={{
                      overflow: 'hidden',
                      maxHeight: servicesOpen ? `${services.length * 60}px` : '0',
                      transition: 'max-height 280ms cubic-bezier(.4,0,.2,1)',
                    }}>
                      <ul style={{ listStyle: 'none', padding: '4px 0 8px 8px', margin: 0 }}>
                        {services.map(s => (
                          <li key={s.label}>
                            <a
                              href={s.href}
                              onClick={close}
                              style={{
                                display: 'flex', alignItems: 'center', gap: '12px',
                                padding: '10px 12px',
                                borderRadius: 'var(--radius-md)',
                                textDecoration: 'none', color: 'inherit',
                                transition: 'background 150ms',
                              }}
                              onMouseEnter={e => (e.currentTarget.style.background = 'var(--surface-2)')}
                              onMouseLeave={e => (e.currentTarget.style.background = 'none')}
                            >
                              <span style={{
                                width: '32px', height: '32px', borderRadius: 'var(--radius-sm)',
                                background: 'var(--surface-2)',
                                display: 'flex', alignItems: 'center', justifyContent: 'center',
                                fontSize: '16px', flexShrink: 0,
                              }}>{s.icon}</span>
                              <span>
                                <span style={{ fontSize: '13px', fontWeight: 600, display: 'block', color: 'var(--text-primary)' }}>{s.label}</span>
                                <span style={{ fontSize: '11px', color: 'var(--text-muted)' }}>{s.sub}</span>
                              </span>
                            </a>
                          </li>
                        ))}
                      </ul>
                    </div>
                  </div>
                ) : (
                  <a
                    href={link.href}
                    onClick={close}
                    style={{
                      display: 'block', padding: '12px 16px',
                      fontFamily: 'var(--font-display)',
                      fontSize: '15px', fontWeight: 700,
                      color: 'var(--text-primary)',
                      textDecoration: 'none',
                      borderRadius: 'var(--radius-md)',
                      transition: 'background 150ms, color 150ms',
                    }}
                    onMouseEnter={e => {
                      (e.currentTarget as HTMLElement).style.background = 'var(--primary-light)';
                      (e.currentTarget as HTMLElement).style.color = 'var(--primary)';
                    }}
                    onMouseLeave={e => {
                      (e.currentTarget as HTMLElement).style.background = 'none';
                      (e.currentTarget as HTMLElement).style.color = 'var(--text-primary)';
                    }}
                  >
                    {link.label}
                  </a>
                )}
              </li>
            ))}
          </ul>
        </nav>

        {/* Footer actions */}
        <div style={{
          padding: '16px 20px',
          borderTop: '1px solid var(--border)',
          display: 'flex', flexDirection: 'column', gap: '10px',
          position: 'sticky', bottom: 0,
          background: 'var(--surface)',
        }}>
          <a
            href={`tel:${phone.replace(/\s/g, '')}`}
            className="btn-primary"
            style={{ justifyContent: 'center', textDecoration: 'none' }}
          >
            📞 {phone}
          </a>
          <a
            href="/contact"
            className="btn-outline"
            style={{ justifyContent: 'center', textDecoration: 'none' }}
            onClick={close}
          >
            Get a Free Quote
          </a>
        </div>
      </div>
    </>
  );
}
