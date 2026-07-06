// Run: node scripts/favicons.mjs
// Requires: npm install -D sharp
import sharp from 'sharp';
import { mkdir, writeFile } from 'fs/promises';
import { existsSync } from 'fs';

// Pack PNG buffers into a single .ico container (PNG-compressed entries,
// supported by every modern browser and Windows Vista+).
function buildIco(entries) {
  const count = entries.length;
  const header = Buffer.alloc(6);
  header.writeUInt16LE(0, 0); // reserved
  header.writeUInt16LE(1, 2); // type: icon
  header.writeUInt16LE(count, 4);

  const dirEntries = [];
  let offset = 6 + count * 16;
  for (const { size, buffer } of entries) {
    const dir = Buffer.alloc(16);
    dir.writeUInt8(size >= 256 ? 0 : size, 0); // width  (0 = 256)
    dir.writeUInt8(size >= 256 ? 0 : size, 1); // height (0 = 256)
    dir.writeUInt8(0, 2);                      // palette colours
    dir.writeUInt8(0, 3);                      // reserved
    dir.writeUInt16LE(1, 4);                   // colour planes
    dir.writeUInt16LE(32, 6);                  // bits per pixel
    dir.writeUInt32LE(buffer.length, 8);       // data size
    dir.writeUInt32LE(offset, 12);             // data offset
    dirEntries.push(dir);
    offset += buffer.length;
  }
  return Buffer.concat([header, ...dirEntries, ...entries.map(e => e.buffer)]);
}

const SRC = 'public/logo.png';
const OUT = 'public/icons';

// solidBg: iOS shows transparent apple-touch-icon backgrounds as black,
// so home-screen icons get a solid white background.
const targets = [
  { file: 'favicon-16x16.png',          size: 16  },
  { file: 'favicon-32x32.png',          size: 32  },
  { file: 'favicon-48x48.png',          size: 48  },
  { file: 'apple-touch-icon.png',       size: 180, solidBg: true },
  { file: 'android-chrome-192x192.png', size: 192 },
  { file: 'android-chrome-512x512.png', size: 512 },
  { file: 'mstile-150x150.png',         size: 150, solidBg: true },
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
  const bg = t.solidBg
    ? { r:255, g:255, b:255, alpha:1 }
    : { r:255, g:255, b:255, alpha:0 };
  let img = sharp(SRC).resize(t.size, t.size, { fit: 'contain', background: bg });
  if (t.solidBg) img = img.flatten({ background: '#ffffff' });
  await img.png().toFile(`${OUT}/${t.file}`);
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

// Multi-size favicon.ico (16 + 32 + 48)
const icoEntries = [];
for (const size of [16, 32, 48]) {
  const buffer = await sharp(SRC)
    .resize(size, size, { fit: 'contain', background: { r:255,g:255,b:255,alpha:0 } })
    .png()
    .toBuffer();
  icoEntries.push({ size, buffer });
}
await writeFile('public/favicon.ico', buildIco(icoEntries));
console.log('✓ favicon.ico (16+32+48 multi-size)');

console.log('\n✅ All favicons done.');
