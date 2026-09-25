/**
 * Full-page captures of the HTML template and of this theme, for side-by-side
 * comparison (.dev/side-by-side.py composes them).
 *
 *   node .dev/capture.mjs <url> <out.png> [width]
 *
 * The page is scrolled to the bottom and back so lazy images load and every
 * figure has finished counting, and the Playground auto-login is pre-empted so
 * the admin bar is not in the picture.
 */

import { chromium } from 'playwright';

const [ , , url, out, width = '1440' ] = process.argv;
if ( ! url || ! out ) {
	console.error( 'Usage: node .dev/capture.mjs <url> <out.png> [width]' );
	process.exit( 1 );
}

const w = Number( width );
const browser = await chromium.launch();
const context = await browser.newContext( {
	viewport: { width: w, height: 900 },
	deviceScaleFactor: 1,
	isMobile: w <= 480,
	hasTouch: w <= 480,
} );
const host = new URL( url );
await context.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', domain: host.hostname, path: '/' } ] );
const page = await context.newPage();
await page.goto( url, { waitUntil: 'networkidle', timeout: 90000 } ).catch( ( e ) => console.log( 'goto:', e.message ) );
await page.evaluate( async () => {
	document.querySelectorAll( 'img[loading="lazy"]' ).forEach( ( img ) => {
		img.loading = 'eager';
	} );
	for ( let y = 0; y < document.body.scrollHeight; y += 500 ) {
		window.scrollTo( 0, y );
		await new Promise( ( r ) => setTimeout( r, 150 ) );
	}
	await Promise.allSettled( [ ...document.images ].map( ( img ) => img.decode().catch( () => {} ) ) );
} );
// The template's counters run for ten seconds.
await page.waitForTimeout( Number( process.env.SETTLE || 2500 ) );
await page.evaluate( () => window.scrollTo( 0, 0 ) );
await page.waitForTimeout( 800 );
await page.screenshot( { path: out, fullPage: true } );
console.log( out, await page.evaluate( () => document.documentElement.scrollHeight ) );
await browser.close();
