/**
 * Dev-only: bundle the theme CSS/JS and serve dev/preview.html as a static page.
 *
 * This lets the markup + stylesheets be reviewed in a browser without a running
 * WordPress install. It is not part of the distributed theme.
 *
 *   node dev/build-preview.mjs
 *   python3 -m http.server 3000 --bind 0.0.0.0 --directory dev/.build
 */
import fs from 'fs';
import path from 'path';

const root = path.resolve(import.meta.dirname, '..');
const out = path.join(root, 'dev', '.build');

fs.mkdirSync(out, { recursive: true });

const componentsDir = path.join(root, 'assets/css/components');
const cssFiles = [
	'assets/css/base.css',
	'assets/css/layout.css',
	...fs.readdirSync(componentsDir).sort().map((f) => 'assets/css/components/' + f),
	'assets/css/woocommerce.css',
];

const css = cssFiles
	.map((f) => `/* ==== ${f} ==== */\n` + fs.readFileSync(path.join(root, f), 'utf8'))
	.join('\n');

const jsFiles = [
	'assets/js/app.js',
	'assets/js/components/carousel.js',
	'assets/js/components/header.js',
	'assets/js/components/tabs.js',
	'assets/js/components/countdown.js',
	'assets/js/components/newsletter.js',
];

const js = jsFiles.map((f) => fs.readFileSync(path.join(root, f), 'utf8')).join('\n');

fs.writeFileSync(path.join(out, 'bundle.css'), css);
fs.writeFileSync(path.join(out, 'bundle.js'), js);
fs.copyFileSync(path.join(root, 'dev/preview.html'), path.join(out, 'index.html'));
fs.mkdirSync(path.join(out, 'assets/images'), { recursive: true });
fs.copyFileSync(
	path.join(root, 'assets/images/placeholder.svg'),
	path.join(out, 'assets/images/placeholder.svg')
);

console.log(`bundled ${cssFiles.length} css + ${jsFiles.length} js -> dev/.build`);
