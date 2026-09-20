const fs = require('fs');
const path = require('path');
const sharp = require('sharp');
const qrcode = require('qrcode-generator');

const projectRoot = path.resolve(__dirname, '..');
const outputDir = path.join(projectRoot, 'marketing');
const logoPath = path.join(projectRoot, 'wp-content/themes/madelyn-day/assets/images/madelyn-day-logo.png');
const svgPath = path.join(outputDir, 'madelyn-day-website-qr-letter.svg');
const pngPath = path.join(outputDir, 'madelyn-day-website-qr-letter.png');
const destination = 'https://mbcreativepublishingllc.com';

fs.mkdirSync(outputDir, { recursive: true });

const pageWidth = 2550;
const pageHeight = 3300;
const qr = qrcode(0, 'H');
qr.addData(destination);
qr.make();

const moduleCount = qr.getModuleCount();
const moduleSize = 24;
const quietModules = 4;
const qrSize = (moduleCount + quietModules * 2) * moduleSize;
const qrX = Math.round((pageWidth - qrSize) / 2);
const qrY = 1650;
const logoData = fs.readFileSync(logoPath).toString('base64');

let qrModules = '';
for (let row = 0; row < moduleCount; row += 1) {
    for (let column = 0; column < moduleCount; column += 1) {
        if (qr.isDark(row, column)) {
            const x = qrX + (column + quietModules) * moduleSize;
            const y = qrY + (row + quietModules) * moduleSize;
            qrModules += `<rect x="${x}" y="${y}" width="${moduleSize}" height="${moduleSize}"/>`;
        }
    }
}

const svg = `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="${pageWidth}" height="${pageHeight}" viewBox="0 0 ${pageWidth} ${pageHeight}" role="img" aria-labelledby="poster-title poster-description">
  <title id="poster-title">Madelyn Day website QR code</title>
  <desc id="poster-description">A printable page with Madelyn Day's logo and a QR code linking to mbcreativepublishingllc.com.</desc>
  <rect width="${pageWidth}" height="${pageHeight}" fill="#fbf7ef"/>
  <rect x="92" y="92" width="${pageWidth - 184}" height="${pageHeight - 184}" rx="8" fill="none" stroke="#ddd3c6" stroke-width="4"/>
  <image href="data:image/png;base64,${logoData}" x="700" y="150" width="1150" height="732" preserveAspectRatio="xMidYMid meet"/>
  <line x1="925" y1="900" x2="1625" y2="900" stroke="#ae7a32" stroke-width="6"/>
  <text x="1275" y="1085" fill="#30211b" font-family="Georgia, 'Times New Roman', serif" font-size="92" font-weight="700" text-anchor="middle">
    <tspan x="1275" dy="0">Scan this QR code to be redirected</tspan>
    <tspan x="1275" dy="115">to Madelyn Day's website</tspan>
  </text>
  <text x="1275" y="1425" fill="#74675f" font-family="Arial, Helvetica, sans-serif" font-size="52" text-anchor="middle">
    See Madelyn Day's books, and purchase them on Amazon.com
  </text>
  <rect x="${qrX}" y="${qrY}" width="${qrSize}" height="${qrSize}" rx="18" fill="#ffffff" stroke="#e5dccf" stroke-width="4"/>
  <g fill="#281813">${qrModules}</g>
  <text x="1275" y="${qrY + qrSize + 125}" fill="#30211b" font-family="Arial, Helvetica, sans-serif" font-size="42" font-weight="700" letter-spacing="2" text-anchor="middle">MBCREATIVEPUBLISHINGLLC.COM</text>
  <line x1="925" y1="${pageHeight - 235}" x2="1625" y2="${pageHeight - 235}" stroke="#ae7a32" stroke-width="6"/>
</svg>`;

fs.writeFileSync(svgPath, svg);

sharp(Buffer.from(svg))
    .png({ compressionLevel: 9, palette: false })
    .withMetadata({ density: 300 })
    .toFile(pngPath)
    .then(() => {
        console.log(`Created ${path.relative(projectRoot, svgPath)}`);
        console.log(`Created ${path.relative(projectRoot, pngPath)}`);
        console.log(`QR destination: ${destination}`);
    })
    .catch((error) => {
        console.error(error);
        process.exitCode = 1;
    });
