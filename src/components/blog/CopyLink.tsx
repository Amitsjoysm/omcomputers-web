import { useState } from 'react';

interface Props {
  url: string;
}

export default function CopyLink({ url }: Props) {
  const [copied, setCopied] = useState(false);

  const handleCopy = async () => {
    try {
      await navigator.clipboard.writeText(url);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch (_) {
      // Fallback for older browsers
      const el = document.createElement('input');
      el.value = url;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  return (
    <button
      onClick={handleCopy}
      style={{
        display: 'inline-flex',
        alignItems: 'center',
        gap: '6px',
        padding: '8px 16px',
        borderRadius: 'var(--radius-sm)',
        border: '1px solid var(--border)',
        background: copied ? 'var(--primary-light)' : 'transparent',
        color: copied ? 'var(--primary-dark)' : 'var(--text-secondary)',
        fontWeight: 600,
        fontSize: '13px',
        cursor: 'pointer',
        transition: 'all 150ms',
        fontFamily: 'var(--font-body)',
      }}
      aria-label={copied ? 'Link copied' : 'Copy link'}
    >
      {copied ? (
        <>✓ Copied!</>
      ) : (
        <>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
          </svg>
          Copy Link
        </>
      )}
    </button>
  );
}
