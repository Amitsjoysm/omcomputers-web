import { useState } from 'react';

interface Post {
  slug: string;
  data: {
    title: string;
    excerpt: string;
    tags: string[];
    publishDate: string;
    coverImage?: { src: string; width?: number; height?: number };
  };
}

interface Props {
  posts: Post[];
  tags: string[];
}

export default function TagFilter({ posts, tags }: Props) {
  const [activeTag, setActiveTag] = useState<string | null>(null);

  const filtered = activeTag
    ? posts.filter(p => p.data.tags.includes(activeTag))
    : posts;

  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });

  return (
    <div>
      {/* Tag pills */}
      <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px', marginBottom: '40px' }}>
        <button
          onClick={() => setActiveTag(null)}
          style={{
            padding: '6px 16px',
            borderRadius: '99px',
            border: '2px solid',
            borderColor: !activeTag ? 'var(--primary)' : 'var(--border)',
            background: !activeTag ? 'var(--primary)' : 'transparent',
            color: !activeTag ? '#fff' : 'var(--text-secondary)',
            fontWeight: 600,
            fontSize: '13px',
            cursor: 'pointer',
            transition: 'all 150ms',
            fontFamily: 'var(--font-body)',
          }}
        >
          All Posts ({posts.length})
        </button>
        {tags.map(tag => (
          <button
            key={tag}
            onClick={() => setActiveTag(tag === activeTag ? null : tag)}
            style={{
              padding: '6px 16px',
              borderRadius: '99px',
              border: '2px solid',
              borderColor: activeTag === tag ? 'var(--primary)' : 'var(--border)',
              background: activeTag === tag ? 'var(--primary)' : 'transparent',
              color: activeTag === tag ? '#fff' : 'var(--text-secondary)',
              fontWeight: 600,
              fontSize: '13px',
              cursor: 'pointer',
              transition: 'all 150ms',
              fontFamily: 'var(--font-body)',
            }}
          >
            {tag}
          </button>
        ))}
      </div>

      {/* Cards grid */}
      {filtered.length === 0 ? (
        <div style={{ textAlign: 'center', padding: '64px 0' }}>
          <div style={{ fontSize: '48px', marginBottom: '16px' }}>📝</div>
          <h3 style={{ fontFamily: 'var(--font-display)', color: 'var(--text-primary)', marginBottom: '8px' }}>
            No posts found
          </h3>
          <p style={{ color: 'var(--text-secondary)' }}>Try selecting a different tag above.</p>
        </div>
      ) : (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: '32px' }}>
          {filtered.map(post => (
            <a
              key={post.slug}
              href={`/blog/${post.slug}`}
              style={{
                background: 'var(--surface)',
                border: '1px solid var(--border)',
                borderRadius: 'var(--radius-lg)',
                overflow: 'hidden',
                boxShadow: 'var(--shadow-card)',
                display: 'flex',
                flexDirection: 'column',
                textDecoration: 'none',
                color: 'inherit',
                transition: 'transform 200ms ease, box-shadow 200ms ease',
              }}
              onMouseEnter={e => {
                (e.currentTarget as HTMLElement).style.transform = 'translateY(-3px)';
                (e.currentTarget as HTMLElement).style.boxShadow = 'var(--shadow-raise)';
              }}
              onMouseLeave={e => {
                (e.currentTarget as HTMLElement).style.transform = 'translateY(0)';
                (e.currentTarget as HTMLElement).style.boxShadow = 'var(--shadow-card)';
              }}
            >
              {/* Cover */}
              <div style={{
                aspectRatio: '16/9',
                background: 'linear-gradient(135deg, var(--primary-light), var(--border))',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                overflow: 'hidden',
              }}>
                {post.data.coverImage ? (
                  <img
                    src={post.data.coverImage.src}
                    alt={post.data.title}
                    style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block' }}
                  />
                ) : (
                  <span style={{ fontSize: '36px', opacity: .3 }}>📝</span>
                )}
              </div>

              {/* Body */}
              <div style={{ padding: '20px 24px', display: 'flex', flexDirection: 'column', gap: '8px', flex: 1 }}>
                <div style={{ display: 'flex', flexWrap: 'wrap', gap: '4px' }}>
                  {post.data.tags.slice(0, 3).map(tag => (
                    <span key={tag} className="badge badge-primary">{tag}</span>
                  ))}
                </div>
                <h3 style={{
                  fontSize: '17px', fontWeight: 700, fontFamily: 'var(--font-display)',
                  color: 'var(--text-primary)', lineHeight: 1.35,
                  display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden',
                }}>
                  {post.data.title}
                </h3>
                <p style={{
                  fontSize: '14px', color: 'var(--text-secondary)', lineHeight: 1.6,
                  display: '-webkit-box', WebkitLineClamp: 3, WebkitBoxOrient: 'vertical', overflow: 'hidden', flex: 1,
                }}>
                  {post.data.excerpt}
                </p>
                <div style={{
                  display: 'flex', justifyContent: 'space-between',
                  fontSize: '12px', color: 'var(--text-muted)',
                  paddingTop: '8px', borderTop: '1px solid var(--border)',
                  marginTop: 'auto',
                }}>
                  <span>📅 {formatDate(post.data.publishDate)}</span>
                  <span>⏱ 3 min read</span>
                </div>
              </div>
            </a>
          ))}
        </div>
      )}
    </div>
  );
}
