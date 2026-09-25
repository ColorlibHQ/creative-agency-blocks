/**
 * The colorlib.com product page's screenshots, from a Playground with the demo
 * content imported (.dev/blueprint.json runs .dev/demo/import.php).
 *
 *   WP_URL=http://127.0.0.1:9492 node .dev/publish-shots.mjs [outdir]
 *   python3 .dev/publish-shots.py [outdir]      # PNG → JPEG, tiles the palettes
 *
 * Viewport captures at 1440 wide (scaled to 1140 by the Python step), never
 * full-page shots. Visitors who ask for reduced motion see the figures at their
 * final value, so the capture does too instead of catching "0".
 *
 * @package Creative Agency
 */

import { chromium } from 'playwright';
import { readFileSync, readdirSync, mkdirSync } from 'node:fs';
import { join, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve( dirname( fileURLToPath( import.meta.url ) ), '..' );
const site = process.env.WP_URL || 'http://127.0.0.1:9492';
const out = process.argv[ 2 ] || join( root, '.dev/publish/raw' );
mkdirSync( out, { recursive: true } );

// name, path, section selector to start at (null = top), viewport height, dark[, width, gap above the section].
const shots = [
	// The card is 3:2. At 1600 wide the studio photo is tall enough that its
	// play button clears the bottom edge.
	[ 'card', '/', null, 1067, false, 1600 ],
	[ 'home', '/', null, 1100, false ],
	[ 'projects', '/', '.creative-agency-works', 1300, false, 1440, 0 ],
	[ 'services', '/', '.creative-agency-services', 1150, false, 1440, 0 ],
	[ 'contact-form', '/contact/', '.creative-agency-contact-title', 1000, false ],
	[ 'blog', '/blog/', '.creative-agency-with-sidebar', 1100, false ],
	[ 'project-page', '/work/harrow-and-pine/', null, 1020, false ],
	[ 'dark-mode', '/', '.creative-agency-works', 1300, true, 1440, 0 ],
];

const browser = await chromium.launch();

async function open( path, height, dark, width = 1440 ) {
	const context = await browser.newContext( { viewport: { width, height }, reducedMotion: 'reduce' } );
	await context.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', domain: new URL( site ).hostname, path: '/' } ] );
	if ( dark ) {
		// The theme follows the reader's system until they choose; this is the
		// visitor's own switch state, stored where scheme-toggle.js keeps it.
		await context.addInitScript( () => localStorage.setItem( 'creative-agency-scheme', 'dark' ) );
	}
	const page = await context.newPage();
	await page.goto( site + path, { waitUntil: 'networkidle' } );
	await page.evaluate( async () => {
		document.querySelectorAll( 'img[loading="lazy"]' ).forEach( ( img ) => {
			img.loading = 'eager';
		} );
		await Promise.allSettled( [ ...document.images ].map( ( img ) => img.decode().catch( () => {} ) ) );
	} );
	return { context, page };
}

async function scrollTo( page, selector, gap = 40 ) {
	if ( ! selector ) {
		return;
	}
	const y = await page.evaluate( ( [ sel, space ] ) => {
		const el = document.querySelector( sel );
		if ( ! el ) {
			throw new Error( 'missing ' + sel );
		}
		const header = document.querySelector( '.creative-agency-header' );
		// Land the section just under the pinned header.
		return el.getBoundingClientRect().top + window.scrollY - ( header ? header.offsetHeight : 0 ) - space;
	}, [ selector, gap ] );
	await page.evaluate( ( top ) => window.scrollTo( 0, top ), y );
	await page.waitForTimeout( 700 );
}

for ( const [ name, path, selector, height, dark, width, gap ] of shots ) {
	const { context, page } = await open( path, height, dark, width );
	await scrollTo( page, selector, gap );
	await page.waitForTimeout( 400 );
	await page.screenshot( { path: join( out, name + '.png' ) } );
	console.log( name );
	await context.close();
}

// The palettes: the end of the home page -- the counting figures, the photo
// strip and the "Visit our work" band -- under every palette, the band's
// bottom edge on the viewport's.
const dir = join( root, 'styles/colors' );
for ( const file of readdirSync( dir ).sort() ) {
	const data = JSON.parse( readFileSync( join( dir, file ), 'utf8' ) );
	const css = ':root{' + data.settings.color.palette.map( ( p ) => `--wp--preset--color--${ p.slug }:${ p.color };` ).join( '' ) + '}';
	const { context, page } = await open( '/', 960, false );
	await page.addStyleTag( { content: css } );
	await page.evaluate( () => {
		const band = document.querySelector( '.creative-agency-band' ).getBoundingClientRect();
		window.scrollTo( 0, band.bottom + window.scrollY - window.innerHeight );
	} );
	await page.waitForTimeout( 600 );
	await page.screenshot( { path: join( out, 'palette-' + file.replace( '.json', '' ) + '.png' ) } );
	console.log( 'palette ' + data.title );
	await context.close();
}

await browser.close();
