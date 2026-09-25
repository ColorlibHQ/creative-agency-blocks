/**
 * Render a page under every colour variation (and optionally dark mode) by
 * overriding the palette's custom properties client-side — exact, and it
 * changes nothing on the site. Writes one full-page capture per palette.
 *
 *   node .dev/palettes.mjs <url> <outdir> [dark]
 */
import { chromium } from 'playwright';
import { readFileSync, readdirSync, mkdirSync } from 'node:fs';
import { join, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve( dirname( fileURLToPath( import.meta.url ) ), '..' );
const [ , , url, out, dark ] = process.argv;
mkdirSync( out, { recursive: true } );
const dir = join( root, 'styles/colors' );
const browser = await chromium.launch();
for ( const file of readdirSync( dir ).sort() ) {
	const data = JSON.parse( readFileSync( join( dir, file ), 'utf8' ) );
	const css = ':root{' + data.settings.color.palette.map( ( p ) => `--wp--preset--color--${ p.slug }:${ p.color };` ).join( '' ) + '}';
	const context = await browser.newContext( { viewport: { width: 1440, height: 900 }, reducedMotion: 'reduce' } );
	await context.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', domain: new URL( url ).hostname, path: '/' } ] );
	const page = await context.newPage();
	await page.goto( url, { waitUntil: 'networkidle' } );
	await page.addStyleTag( { content: css } );
	if ( dark ) {
		// The injected palette sits on :root, as scheme.css's overrides do, so
		// scheme.css goes in again after it or the palette would win.
		await page.addStyleTag( { content: readFileSync( join( root, 'assets/css/scheme.css' ), 'utf8' ) } );
		await page.evaluate( () => document.documentElement.classList.add( 'creative-agency-dark' ) );
	}
	await page.evaluate( async () => {
		for ( let y = 0; y < document.body.scrollHeight; y += 700 ) {
			window.scrollTo( 0, y );
			await new Promise( ( r ) => setTimeout( r, 80 ) );
		}
		window.scrollTo( 0, 0 );
	} );
	await page.waitForTimeout( 600 );
	const name = file.replace( '.json', '' ) + ( dark ? '-dark' : '' );
	await page.screenshot( { path: join( out, name + '.png' ), fullPage: true } );
	console.log( name );
	await context.close();
}
await browser.close();
