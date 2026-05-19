import { useState } from 'react';

interface PriceItem {
  name: string;
  brand: string;
  specs: string;
  price: number;
  inStock: boolean;
  image?: { src: string };
}

interface Category {
  id: string;
  data: {
    category: string;
    items: PriceItem[];
  };
}

interface Props {
  categories: Category[];
  whatsapp: string;
}

type SortKey = 'name' | 'price-asc' | 'price-desc';

export default function CategoryFilter({ categories, whatsapp }: Props) {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [sort, setSort] = useState<SortKey>('name');

  const allItems: Array<PriceItem & { categoryLabel: string }> = categories.flatMap(cat =>
    cat.data.items.map(item => ({ ...item, categoryLabel: cat.data.category }))
  );

  const filtered = activeCategory === 'all'
    ? allItems
    : categories
        .find(c => c.id === activeCategory)
        ?.data.items.map(item => ({ ...item, categoryLabel: activeCategory })) ?? [];

  const sorted = [...filtered].sort((a, b) => {
    if (sort === 'price-asc') return a.price - b.price;
    if (sort === 'price-desc') return b.price - a.price;
    return a.name.localeCompare(b.name);
  });

  const formatINR = (n: number) =>
    new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(n);

  const waLink = (item: PriceItem) => {
    const text = encodeURIComponent(
      `Hi OM Computers! I'm interested in: ${item.name} (${item.brand}) - ${formatINR(item.price)}. Please confirm availability.`
    );
    return `https://wa.me/${whatsapp}?text=${text}`;
  };

  const tabStyle = (active: boolean): React.CSSProperties => ({
    padding: '8px 18px',
    borderRadius: '99px',
    border: '2px solid',
    borderColor: active ? 'var(--primary)' : 'var(--border)',
    background: active ? 'var(--primary)' : 'transparent',
    color: active ? '#fff' : 'var(--text-secondary)',
    fontWeight: 600,
    fontSize: '14px',
    cursor: 'pointer',
    transition: 'all 150ms',
    fontFamily: 'var(--font-body)',
    whiteSpace: 'nowrap' as const,
  });

  return (
    <div>
      {/* Filters row */}
      <div style={{ display: 'flex', flexWrap: 'wrap', gap: '12px', marginBottom: '32px', alignItems: 'center', justifyContent: 'space-between' }}>
        <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
          <button style={tabStyle(activeCategory === 'all')} onClick={() => setActiveCategory('all')}>
            All ({allItems.length})
          </button>
          {categories.map(cat => (
            <button
              key={cat.id}
              style={tabStyle(activeCategory === cat.id)}
              onClick={() => setActiveCategory(cat.id)}
            >
              {cat.data.category} ({cat.data.items.length})
            </button>
          ))}
        </div>

        <select
          value={sort}
          onChange={e => setSort(e.target.value as SortKey)}
          style={{
            padding: '8px 14px',
            borderRadius: 'var(--radius-sm)',
            border: '1px solid var(--border)',
            background: 'var(--surface)',
            fontSize: '14px',
            fontFamily: 'var(--font-body)',
            color: 'var(--text-primary)',
            cursor: 'pointer',
          }}
          aria-label="Sort products"
        >
          <option value="name">Name A–Z</option>
          <option value="price-asc">Price Low–High</option>
          <option value="price-desc">Price High–Low</option>
        </select>
      </div>

      {/* Product grid */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(240px, 1fr))', gap: '24px' }}>
        {sorted.map((item, i) => (
          <article
            key={`${item.name}-${i}`}
            style={{
              background: 'var(--surface)',
              border: '1px solid var(--border)',
              borderRadius: 'var(--radius-lg)',
              padding: '24px',
              boxShadow: 'var(--shadow-card)',
              display: 'flex',
              flexDirection: 'column',
              gap: '12px',
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
            {/* Image */}
            <div style={{
              width: '150px', height: '150px',
              background: 'var(--surface-2)',
              borderRadius: 'var(--radius-md)',
              border: '1px solid var(--border)',
              alignSelf: 'center',
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              overflow: 'hidden',
            }}>
              {item.image ? (
                <img src={item.image.src} alt={item.name} style={{ width: '100%', height: '100%', objectFit: 'contain', padding: '8px' }} />
              ) : (
                <span style={{ fontSize: '40px' }}>🔧</span>
              )}
            </div>

            <div>
              <span className="badge badge-primary" style={{ marginBottom: '6px', display: 'inline-block' }}>{item.brand}</span>
              <h3 style={{ fontSize: '15px', fontWeight: 700, fontFamily: 'var(--font-display)', lineHeight: 1.3 }}>{item.name}</h3>
            </div>

            <p style={{ fontSize: '12px', fontFamily: 'var(--font-mono)', color: 'var(--text-secondary)', lineHeight: 1.5 }}>{item.specs}</p>

            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '8px', flexWrap: 'wrap', marginTop: 'auto' }}>
              <span style={{ fontSize: '22px', fontWeight: 700, color: 'var(--primary)', fontFamily: 'var(--font-mono)' }}>
                {formatINR(item.price)}
              </span>
              <span className={item.inStock ? 'badge badge-success' : 'badge badge-muted'}>
                {item.inStock ? '✓ In Stock' : 'Out of Stock'}
              </span>
            </div>

            <a
              href={waLink(item)}
              target="_blank"
              rel="noopener"
              style={{
                display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '6px',
                padding: '10px 16px',
                borderRadius: 'var(--radius-sm)',
                background: 'var(--primary)', color: '#fff', fontWeight: 600,
                fontSize: '13px', textDecoration: 'none',
                transition: 'background 150ms',
                fontFamily: 'var(--font-body)',
              }}
              onMouseEnter={e => { (e.currentTarget as HTMLElement).style.background = 'var(--primary-dark)'; }}
              onMouseLeave={e => { (e.currentTarget as HTMLElement).style.background = 'var(--primary)'; }}
            >
              Request Quote
            </a>
          </article>
        ))}
      </div>
    </div>
  );
}
