import { useState, useEffect } from 'react';

// ── CSS injected once ─────────────────────────────────────────────────────────
const CSS = `
  @keyframes om-spin   { to { transform: rotate(360deg); } }
  @keyframes om-spin2  { to { transform: rotate(-360deg); } }
  @keyframes om-pulse  { 0%,100%{opacity:.4;transform:scale(.95)} 50%{opacity:1;transform:scale(1)} }
  @keyframes om-dot    { 0%,80%,100%{transform:scale(0);opacity:.3} 40%{transform:scale(1);opacity:1} }
  @keyframes h-fadein  { from{opacity:0} to{opacity:1} }
  @keyframes h-up      { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
  @keyframes h-shape   { from{opacity:0;transform:translateY(-40px)} to{opacity:1;transform:translateY(0)} }
  @keyframes h-float   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(10px)} }

  @media (prefers-reduced-motion: reduce) {
    @keyframes h-up    { from{opacity:0} to{opacity:1} }
    @keyframes h-shape { from{opacity:0} to{opacity:1} }
    @keyframes h-float { from{} to{} }
  }

  .h-card {
    transition: transform 200ms ease, box-shadow 200ms ease, border-color 180ms;
  }
  .h-card:hover { transform: translateY(-3px) scale(1.02); }

  .h-btn-primary {
    transition: background 150ms, box-shadow 150ms, transform 150ms, border-color 150ms;
  }
  .h-btn-primary:hover {
    background: #1565C0 !important;
    border-color: #1565C0 !important;
    box-shadow: 0 6px 22px rgba(30,136,229,.40) !important;
    transform: translateY(-1px);
  }
  .h-btn-outline { transition: background 150ms; }
  .h-btn-outline:hover { background: #E3F2FD !important; }
  .h-btn-wa { transition: background 150ms; }
  .h-btn-wa:hover { background: #D5F5E3 !important; }
`;

// ── Spinner ───────────────────────────────────────────────────────────────────
function OmSpinner() {
  return (
    <div style={{
      minHeight: 'calc(100vh - 76px)',
      display: 'flex', flexDirection: 'column',
      alignItems: 'center', justifyContent: 'center',
      background: '#ffffff', gap: 24,
    }}>
      <div style={{ position: 'relative', width: 88, height: 88 }}>
        <div style={{
          position: 'absolute', inset: -8, borderRadius: '50%',
          border: '2px solid #E3F2FD', borderTopColor: '#1E88E5',
          animation: 'om-spin 2.4s linear infinite',
        }} />
        <div style={{
          position: 'absolute', inset: 2, borderRadius: '50%',
          border: '2px solid #F5F7FA', borderBottomColor: '#1565C0',
          animation: 'om-spin2 1.4s linear infinite',
        }} />
        <div style={{
          position: 'absolute', inset: 10, background: '#EBF5FE', borderRadius: '50%',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          animation: 'om-pulse 2s ease-in-out infinite',
        }}>
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
            stroke="#1E88E5" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2" />
            <line x1="8" y1="21" x2="16" y2="21" />
            <line x1="12" y1="17" x2="12" y2="21" />
          </svg>
        </div>
      </div>
      <div style={{ textAlign: 'center' }}>
        <div style={{
          fontFamily: "'Plus Jakarta Sans', sans-serif",
          fontSize: 17, fontWeight: 800, color: '#0D1B2A', letterSpacing: '-0.01em',
        }}>
          OM Computers
        </div>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 5, marginTop: 7 }}>
          {[0, 0.18, 0.36].map(d => (
            <span key={d} style={{
              width: 6, height: 6, borderRadius: '50%', background: '#1E88E5',
              display: 'inline-block',
              animation: `om-dot 1.4s ease-in-out ${d}s infinite`,
            }} />
          ))}
        </div>
      </div>
    </div>
  );
}

// ── Data ──────────────────────────────────────────────────────────────────────
const SERVICES = [
  { icon: '🖥️', label: 'Device Repair',   sub: 'Laptops · Desktops',  href: '/services/device-repair',    accent: '#1E88E5', bg: '#E3F2FD' },
  { icon: '📡', label: 'CCTV Systems',     sub: 'HD · IP · NVR',       href: '/services/cctv-surveillance', accent: '#2ECC71', bg: '#E8F8EF' },
  { icon: '🔐', label: 'Biometric Access', sub: 'Attendance · Doors',  href: '/services/biometric-access',  accent: '#F5A623', bg: '#FFF8E7' },
  { icon: '💻', label: 'IT Support',       sub: 'AMC · Helpdesk',      href: '/services/it-support-amc',    accent: '#8E44AD', bg: '#F5EEF8' },
  { icon: '🌐', label: 'Web Services',     sub: 'Sites · SEO · Ads',   href: '/services/web-digital',       accent: '#00ACC1', bg: '#E0F7FA' },
  { icon: '🔧', label: 'Hardware Parts',   sub: 'RAM · SSD · More',    href: '/parts',                      accent: '#E53935', bg: '#FEECEC' },
];

const TRUST = ['Genuine Parts Only', 'Same-Day Diagnosis', '6-Month Warranty', 'Free Assessment'];

const SHAPES = [
  { w: 480, h: 100, r: 9,   delay: 0.1,  color: 'rgba(30,136,229,0.09)', top: '8%',   left: '-6%'  },
  { w: 340, h: 80,  r: -13, delay: 0.22, color: 'rgba(21,101,192,0.07)', top: '65%',  right: '-5%' },
  { w: 220, h: 56,  r: -6,  delay: 0.32, color: 'rgba(0,172,193,0.08)',  bottom: '6%',left: '10%'  },
  { w: 140, h: 40,  r: 18,  delay: 0.40, color: 'rgba(30,136,229,0.07)', top: '6%',   right: '22%' },
];

// ── Hero ──────────────────────────────────────────────────────────────────────
interface HeroProps { phone: string; whatsapp: string; }

export default function Hero({ phone, whatsapp }: HeroProps) {
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    // Snap to top before revealing hero so page never appears scrolled down
    window.scrollTo({ top: 0, behavior: 'instant' as ScrollBehavior });
    setMounted(true);
  }, []);

  if (!mounted) {
    return (
      <>
        <style>{CSS}</style>
        <OmSpinner />
      </>
    );
  }

  return (
    <>
      <style>{CSS}</style>
      <section
        aria-label="Hero"
        style={{
          position: 'relative', overflow: 'hidden',
          background: '#ffffff',
          display: 'flex', alignItems: 'center',
          padding: '28px 0 40px',
          borderBottom: '1px solid #E0E7EF',
          animation: 'h-fadein 0.3s ease both',
        }}
      >
        {/* Gradient glows */}
        <div aria-hidden="true" style={{
          position: 'absolute', inset: 0, pointerEvents: 'none',
          background: 'radial-gradient(ellipse 65% 60% at 80% 0%, rgba(30,136,229,0.09) 0%, transparent 70%)',
        }} />
        <div aria-hidden="true" style={{
          position: 'absolute', inset: 0, pointerEvents: 'none',
          background: 'radial-gradient(ellipse 45% 40% at 5% 100%, rgba(30,136,229,0.06) 0%, transparent 65%)',
        }} />
        {/* Dot grid */}
        <div aria-hidden="true" style={{
          position: 'absolute', inset: 0, pointerEvents: 'none',
          backgroundImage: 'radial-gradient(circle, rgba(30,136,229,0.10) 1px, transparent 1px)',
          backgroundSize: '30px 30px',
        }} />

        {/* Floating shapes — pure CSS */}
        {SHAPES.map((s, i) => (
          <div key={i} aria-hidden="true" style={{
            position: 'absolute',
            top: s.top, left: (s as any).left, right: (s as any).right, bottom: (s as any).bottom,
            pointerEvents: 'none',
            animation: `h-shape 1.6s ${s.delay}s ease both, h-float 10s ${s.delay + 1.6}s ease-in-out infinite`,
          }}>
            <div style={{
              width: s.w, height: s.h,
              borderRadius: 9999,
              transform: `rotate(${s.r}deg)`,
              background: `linear-gradient(135deg, ${s.color}, transparent)`,
              border: '1px solid rgba(30,136,229,0.10)',
            }} />
          </div>
        ))}

        <div style={{ maxWidth: 1200, margin: '0 auto', padding: '0 24px', width: '100%', position: 'relative', zIndex: 1 }}>
          <div className="hero-grid">

            {/* LEFT */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: 18 }}>

              {/* Badge */}
              <div style={{ animation: 'h-up 0.55s 0.04s ease both' }}>
                <span style={{
                  display: 'inline-flex', alignItems: 'center', gap: 8,
                  padding: '5px 13px',
                  background: '#E3F2FD', border: '1px solid rgba(30,136,229,0.25)',
                  borderRadius: 999, fontSize: 12, fontWeight: 700,
                  letterSpacing: '0.06em', textTransform: 'uppercase',
                  color: '#1565C0', fontFamily: 'var(--font-body)',
                }}>
                  <span style={{
                    width: 6, height: 6, borderRadius: '50%', background: '#1E88E5',
                    display: 'inline-block', boxShadow: '0 0 0 3px rgba(30,136,229,0.2)',
                  }} />
                  Trusted IT Experts · Pune, Maharashtra
                </span>
              </div>

              {/* Headline */}
              <h1 style={{
                fontFamily: 'var(--font-display)',
                fontSize: 'clamp(34px, 4.8vw, 56px)',
                fontWeight: 800, lineHeight: 1.09, margin: 0,
                color: '#0D1B2A', letterSpacing: '-0.02em',
                animation: 'h-up 0.55s 0.12s ease both',
              }}>
                Repair.{' '}
                <span style={{
                  backgroundImage: 'linear-gradient(135deg, #1E88E5 0%, #1565C0 55%, #0D47A1 100%)',
                  WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent', backgroundClip: 'text',
                }}>
                  Upgrade.
                </span>
                {' '}Secure.
                <br />
                <span style={{ fontSize: '65%', fontWeight: 700, color: '#4A5568', letterSpacing: '-0.01em' }}>
                  Your Tech, Our Expertise.
                </span>
              </h1>

              {/* Lead */}
              <p style={{
                fontSize: 16, lineHeight: 1.7, color: '#4A5568',
                maxWidth: 480, margin: 0, fontFamily: 'var(--font-body)',
                animation: 'h-up 0.55s 0.20s ease both',
              }}>
                From cracked laptop screens to full CCTV installations, OM Computers handles every IT need for homes and businesses across Pune.
              </p>

              {/* CTAs */}
              <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap', animation: 'h-up 0.55s 0.27s ease both' }}>
                <a href="/contact" className="h-btn-primary" style={{
                  display: 'inline-flex', alignItems: 'center', gap: 8,
                  padding: '12px 26px', background: '#1E88E5', color: '#fff',
                  fontWeight: 700, fontSize: 15, borderRadius: 8,
                  border: '2px solid #1E88E5',
                  boxShadow: '0 4px 16px rgba(30,136,229,0.28)',
                  textDecoration: 'none', fontFamily: 'var(--font-body)',
                }}>
                  Book a Repair
                </a>
                <a href="/parts" className="h-btn-outline" style={{
                  display: 'inline-flex', alignItems: 'center', gap: 8,
                  padding: '12px 26px', background: '#ffffff', color: '#1E88E5',
                  fontWeight: 700, fontSize: 15, borderRadius: 8,
                  border: '2px solid #1E88E5', textDecoration: 'none',
                  fontFamily: 'var(--font-body)',
                }}>
                  View Parts List
                </a>
              </div>

              {/* Trust strip */}
              <div style={{
                display: 'flex', flexWrap: 'wrap', gap: '6px 16px',
                paddingTop: 14, borderTop: '1px solid #E0E7EF',
                animation: 'h-up 0.55s 0.34s ease both',
              }}>
                {TRUST.map(label => (
                  <span key={label} style={{
                    display: 'inline-flex', alignItems: 'center', gap: 5,
                    fontSize: 13, fontWeight: 600, color: '#4A5568', fontFamily: 'var(--font-body)',
                  }}>
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                      <circle cx="7" cy="7" r="7" fill="#E3F2FD" />
                      <path d="M4 7l2 2 4-4" stroke="#1E88E5" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                    {label}
                  </span>
                ))}
              </div>

              {/* WhatsApp */}
              <div style={{ animation: 'h-up 0.55s 0.40s ease both' }}>
                <a
                  href={`https://wa.me/${whatsapp}?text=${encodeURIComponent('Hi OM Computers! I need IT help.')}`}
                  target="_blank" rel="noopener" className="h-btn-wa"
                  style={{
                    display: 'inline-flex', alignItems: 'center', gap: 9,
                    padding: '8px 14px', background: '#F0FFF4',
                    border: '1px solid #A8E6CF', borderRadius: 999, textDecoration: 'none',
                  }}
                >
                  <svg width="17" height="17" viewBox="0 0 24 24" fill="#25D366">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.558 4.109 1.533 5.83L.057 23.926a.5.5 0 0 0 .609.61l6.253-1.633A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 0 1-5.174-1.448l-.371-.22-3.844 1.005 1.027-3.742-.241-.385A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                  </svg>
                  <span style={{ fontSize: 13, fontWeight: 700, color: '#1A7A43' }}>Chat on WhatsApp</span>
                  <span style={{ fontSize: 12, color: '#9AA5B4', fontFamily: 'var(--font-body)' }}>{phone}</span>
                </a>
              </div>
            </div>

            {/* RIGHT — cards */}
            <div className="hero-cards-panel" aria-label="Our services">
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 9 }}>
                {SERVICES.map((svc, i) => (
                  <a
                    key={svc.label}
                    href={svc.href}
                    className="h-card"
                    style={{
                      display: 'flex', flexDirection: 'column', gap: 9,
                      padding: '14px 13px', background: '#ffffff',
                      border: '1px solid #E0E7EF', borderRadius: 13,
                      textDecoration: 'none',
                      boxShadow: '0 2px 8px rgba(30,136,229,0.07)',
                      animation: `h-up 0.45s ${0.18 + i * 0.07}s ease both`,
                    }}
                    onMouseEnter={e => {
                      const el = e.currentTarget as HTMLElement;
                      el.style.borderColor = svc.accent + '60';
                      el.style.boxShadow = `0 5px 18px ${svc.accent}22`;
                    }}
                    onMouseLeave={e => {
                      const el = e.currentTarget as HTMLElement;
                      el.style.borderColor = '#E0E7EF';
                      el.style.boxShadow = '0 2px 8px rgba(30,136,229,0.07)';
                    }}
                  >
                    <span style={{
                      width: 38, height: 38, background: svc.bg, borderRadius: 9,
                      display: 'flex', alignItems: 'center', justifyContent: 'center',
                      fontSize: 18, flexShrink: 0,
                    }}>
                      {svc.icon}
                    </span>
                    <div>
                      <div style={{
                        fontSize: 13, fontWeight: 700, color: '#0D1B2A',
                        fontFamily: 'var(--font-display)', lineHeight: 1.3,
                      }}>
                        {svc.label}
                      </div>
                      <div style={{ fontSize: 11, color: '#9AA5B4', marginTop: 2, fontFamily: 'var(--font-body)' }}>
                        {svc.sub}
                      </div>
                    </div>
                    <span style={{
                      fontSize: 11, fontWeight: 700, color: svc.accent,
                      display: 'inline-flex', alignItems: 'center', gap: 3,
                    }}>
                      Learn more
                      <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <path d="M2 5h6M6 3l2 2-2 2" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                      </svg>
                    </span>
                  </a>
                ))}
              </div>

              {/* Stats strip */}
              <div style={{
                marginTop: 10, padding: '12px 18px',
                background: '#F5F7FA', border: '1px solid #E0E7EF', borderRadius: 11,
                display: 'flex', alignItems: 'center',
                animation: 'h-up 0.50s 0.68s ease both',
              }}>
                {[
                  { val: '500+', label: 'Devices repaired' },
                  { val: '6 Mo',  label: 'Repair warranty'  },
                  { val: '1 Day', label: 'Turnaround'       },
                ].map((s, i) => (
                  <div key={s.val} style={{ display: 'flex', alignItems: 'center', flex: 1 }}>
                    {i > 0 && <div style={{ width: 1, height: 26, background: '#E0E7EF', marginRight: 14 }} />}
                    <div style={{ flex: 1, textAlign: 'center' }}>
                      <div style={{
                        fontSize: 19, fontWeight: 800, color: '#1E88E5',
                        fontFamily: 'var(--font-display)', lineHeight: 1,
                      }}>
                        {s.val}
                      </div>
                      <div style={{ fontSize: 10, color: '#9AA5B4', marginTop: 3, fontWeight: 500, fontFamily: 'var(--font-body)' }}>
                        {s.label}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>

        <style>{`
          .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 48px;
            align-items: center;
          }
          .hero-cards-panel { display: block; }
          @media (max-width: 960px) {
            .hero-grid { grid-template-columns: 1fr; gap: 36px; }
            .hero-cards-panel { display: none; }
          }
        `}</style>
      </section>
    </>
  );
}
