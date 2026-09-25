/**
 * Open every theme pattern in the block editor and look at what an owner sees.
 *
 * validate-blocks.mjs parses markup; this renders it in the real editor
 * canvas, which is where Unioncorp's icon tiles turned out to read "Type / to
 * choose a block" while the front end looked perfect. For each pattern it
 * reports: blocks the editor calls invalid or missing, any rich-text
 * placeholder the editor shows (an empty paragraph asking to be typed into),
 * and any icon class whose mask did not draw. With SHOTS=<dir> it also saves
 * a screenshot of the canvas per pattern.
 *
 *   WP_URL=http://127.0.0.1:9492 WP_USER=admin WP_PASS=password node .dev/editor-check.mjs
 */

import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';

const url = process.env.WP_URL || 'http://127.0.0.1:9492';
const shots = process.env.SHOTS || '';
if ( shots ) {
	mkdirSync( shots, { recursive: true } );
}

const browser = await chromium.launch();
const page = await ( await browser.newContext( { viewport: { width: 1440, height: 1000 } } ) ).newPage();
await page.goto( url + '/wp-login.php', { waitUntil: 'commit' } );
await page.fill( '#user_login', process.env.WP_USER || 'admin' );
await page.fill( '#user_pass', process.env.WP_PASS || 'password' );
await page.click( '#wp-submit' );
await page.waitForFunction( () => 'complete' === document.readyState );

await page.goto( url + '/wp-admin/post-new.php?post_type=page', { waitUntil: 'domcontentloaded', timeout: 90000 } );
await page.waitForFunction( () => window.wp && window.wp.data && window.wp.data.select( 'core/block-editor' ), null, { timeout: 90000 } );
await page.waitForTimeout( 4000 );
// Close the welcome guide and the pattern chooser if they open.
await page.evaluate( () => {
	try {
		window.wp.data.dispatch( 'core/preferences' ).set( 'core/edit-post', 'welcomeGuide', false );
	} catch ( e ) {}
} );
await page.keyboard.press( 'Escape' );

const patterns = await page.evaluate( async () => {
	const all = await window.wp.apiFetch( { path: '/wp/v2/block-patterns/patterns' } );
	return all.filter( ( p ) => p.name.startsWith( 'creative-agency/' ) ).map( ( p ) => ( { name: p.name, content: p.content } ) );
} );

const problems = [];
for ( const pattern of patterns ) {
	const invalid = await page.evaluate( ( content ) => {
		const blocks = window.wp.blocks.parse( content );
		const bad = [];
		const walk = ( list ) => list.forEach( ( b ) => {
			if ( false === b.isValid ) {
				bad.push( 'invalid ' + b.name );
			}
			if ( 'core/missing' === b.name ) {
				bad.push( 'missing ' + ( b.attributes.originalName || '?' ) );
			}
			walk( b.innerBlocks || [] );
		} );
		walk( blocks );
		window.wp.data.dispatch( 'core/block-editor' ).resetBlocks( blocks );
		return bad;
	}, pattern.content );
	await page.waitForTimeout( 1500 );

	const frame = page.frame( { name: 'editor-canvas' } ) || page.mainFrame();
	const seen = await frame.evaluate( () => {
		const out = { placeholders: [], icons: 0, blankIcons: [] };
		// Only text blocks the theme wrote: an empty paragraph or heading is
		// what reads "Type / to choose a block". Dynamic blocks' own prompts
		// ("Add title" on the page itself, "Older Comments", "(no title)") are
		// core's editing affordances, not missing content.
		document.querySelectorAll( '.wp-block-paragraph[data-rich-text-placeholder], .wp-block-heading[data-rich-text-placeholder]' ).forEach( ( el ) => {
			const ph = el.getAttribute( 'data-rich-text-placeholder' ) || '';
			if ( ph.trim() && ! el.textContent.trim() && el.getBoundingClientRect().width > 0 ) {
				out.placeholders.push( ph.slice( 0, 40 ) );
			}
		} );
		document.querySelectorAll( '[class*="creative-agency-icon--"]' ).forEach( ( el ) => {
			out.icons++;
			const s = getComputedStyle( el, '::before' );
			const mask = s.maskImage || s.webkitMaskImage || '';
			if ( ! mask || 'none' === mask || parseFloat( s.width ) < 4 ) {
				out.blankIcons.push( el.className.slice( 0, 60 ) );
			}
		} );
		return out;
	} );

	if ( shots ) {
		const canvas = await page.$( 'iframe[name="editor-canvas"]' );
		if ( canvas ) {
			await canvas.screenshot( { path: `${ shots }/${ pattern.name.split( '/' )[ 1 ] }.png` } );
		}
	}

	const issues = [ ...invalid, ...seen.placeholders.map( ( p ) => 'placeholder "' + p + '"' ), ...seen.blankIcons.map( ( i ) => 'icon not drawn: ' + i ) ];
	console.log( `${ issues.length ? 'FAIL' : 'ok  ' } ${ pattern.name.padEnd( 44 ) } icons ${ seen.icons }${ issues.length ? '  ' + issues.join( '; ' ) : '' }` );
	if ( issues.length ) {
		problems.push( pattern.name );
	}
}

await browser.close();
console.log( `\n${ patterns.length } patterns opened in the editor, ${ problems.length } with problems` );
process.exit( problems.length ? 1 : 0 );
