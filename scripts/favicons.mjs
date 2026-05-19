// Run: node scripts/favicons.mjs
// Requires: npm install -D sharp
import sharp from 'sharp';
import { mkdir, access } from 'fs/promises';
import { existsSync } from 'fs';

const SRC = 'public/logo.png';
const OUT = 'public/icons';

const targets = [
  { file: 'favicon-16x16.png',          size: 16  },
  { file: 'favicon-32x32.png',          size: 32  },
  { file: 'favicon-48x48.png',          size: 48  },
  { file: 'apple-touch-icon.png',       size: 180 },
  { file: 'android-chrome-192x192.png', size: 192 },
  { file: 'android-chrome-512x512.png', size: 512 },
  { file: 'mstile-150x150.png',         size: 150 },
];

await mkdir(OUT, { recursive: true });

// Check if source logo exists
if (!existsSync(SRC)) {
  console.log(`⚠️  ${SRC} not found — creating placeholder favicons`);
  // Create a simple placeholder if logo doesn't exist
  const placeholder = await sharp({
    create: {
      width: 512, height: 512, channels: 4,
      background: { r: 30, g: 136, b: 229, alpha: 1 },
    }
  }).png().toBuffer();

  for (const t of targets) {
    await sharp(placeholder)
      .resize(t.size, t.size, { fit: 'contain', background: { r:255,g:255,b:255,alpha:0 } })
      .png()
      .toFile(`${OUT}/${t.file}`);
    console.log(`✓ ${t.file} (placeholder)`);
  }

  await sharp({ create: { width:1200, height:630, channels:4, background: { r:30,g:136,b:229,alpha:1 } } })
    .jpeg({ quality: 90 })
    .toFile(`${OUT}/og-image.jpg`);
  console.log('✓ og-image.jpg (placeholder)');
  console.log('\n⚠️  Add your real logo.png to public/ and re-run this script.');
  process.exit(0);
}

for (const t of targets) {
  await sharp(SRC)
    .resize(t.size, t.size, { fit: 'contain', background: { r:255,g:255,b:255,alpha:0 } })
    .png()
    .toFile(`${OUT}/${t.file}`);
  console.log(`✓ ${t.file}`);
}

// OG image — logo centred on white 1200×630
const logoBuffer = await sharp(SRC).resize(400).toBuffer();
await sharp({
  create: { width:1200, height:630, channels:4, background: { r:255,g:255,b:255,alpha:1 } }
})
  .composite([{ input: logoBuffer, gravity: 'center' }])
  .jpeg({ quality: 90 })
  .toFile(`${OUT}/og-image.jpg`);
console.log('✓ og-image.jpg');

console.log('\n✅ All favicons done.');
console.log('Copy public/icons/favicon-32x32.png → public/favicon.ico manually if needed.');
