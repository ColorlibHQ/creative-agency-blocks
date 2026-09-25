/**
 * How large each bundled photograph is actually drawn, so the files can be
 * sized for the largest rendered box at 2x (release prep, section 1).
 *
 *   WP_URL=http://127.0.0.1:9492 node .dev/image-sizes.mjs
 *
 * Prints, per file under assets/images/, the largest CSS box it is drawn in
 * across the pages and widths below, its natural size, and the ratio
 * natural / (box x 2): under 1 means the file is soft on a 2x screen.
 *
 * @package Creative Agency
 */

import { chromium } from 'playwright';

const site = process.env.WP_URL || 'http://localhost';
const paths = ( process.env.CREATIVE_AGENCY_PATHS ||
	'/,/about/,/work/,/work/nookdesk/,/work/whisk/,/work/harrow-and-pine/,/work/arcade-club/,/services/,/blog/,/contact/' ).split( ',' );
const widths = [ 1920, 1440, 1024, 390 ];

const browser = await chromium.launch();
const best = {};
for ( const width of widths ) {
	const context = await browser.newContext( { viewport: { width, height: 1000 } } );
	await context.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', url: site } ] );
	const page = await context.newPage();
	for ( const path of paths ) {
		await page.goto( site + path, { waitUntil: 'load' } );
		const found = await page.evaluate( () => {
			const out = [];
			document.querySelectorAll( 'img' ).forEach( ( img ) => {
				const r = img.getBoundingClientRect();
				out.push( { src: img.currentSrc || img.src, w: r.width, h: r.height, nw: img.naturalWidth, nh: img.naturalHeight } );
			} );
			document.querySelectorAll( '.wp-block-cover__image-background' ).forEach( ( img ) => {
				const r = img.getBoundingClientRect();
				out.push( { src: img.currentSrc || img.src, w: r.width, h: r.height, nw: img.naturalWidth, nh: img.naturalHeight } );
			} );
			return out;
		} );
		for ( const f of found ) {
			const m = f.src.match( /assets\/images\/([^?]+)/ ) || f.src.match( /uploads\/.*\/([^/?]+)$/ );
			if ( ! m || ! f.w ) continue;
			const key = m[ 1 ];
			const cur = best[ key ];
			if ( ! cur || f.w * f.h > cur.w * cur.h ) best[ key ] = { ...f, width, path };
		}
	}
	await context.close();
}
await browser.close();
for ( const [ key, f ] of Object.entries( best ).sort() ) {
	const need = Math.round( f.w * 2 );
	console.log( `${ key.padEnd( 34 ) } box ${ Math.round( f.w ) }x${ Math.round( f.h ) } @${ f.width } ${ f.path.padEnd( 26 ) } natural ${ f.nw }x${ f.nh }  2x needs ${ need }w  ratio ${ ( f.nw / need ).toFixed( 2 ) }` );
}
