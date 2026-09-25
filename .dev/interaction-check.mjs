/**
 * The behaviours the design has, exercised in a browser: the header pins
 * (and turns black) past 400px, a figure counts up to its value, the play
 * button opens the film in a dialog and Escape stops it, the dark mode switch
 * flips the scheme and its pressed state, and at phone width the menu button
 * opens the navigation. Also: no console errors on the way.
 *
 *   WP_URL=http://127.0.0.1:9492 node .dev/interaction-check.mjs
 */
import { chromium } from 'playwright';

const base = process.env.WP_URL || 'http://127.0.0.1:9492';
const browser = await chromium.launch();
const results = [];
const ok = ( name, pass, detail = '' ) => results.push( `${ pass ? 'ok  ' : 'FAIL' } ${ name }${ detail ? '  ' + detail : '' }` );

async function open( width ) {
	const context = await browser.newContext( { viewport: { width, height: 900 } } );
	await context.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', domain: new URL( base ).hostname, path: '/' } ] );
	const page = await context.newPage();
	const errors = [];
	page.on( 'pageerror', ( e ) => errors.push( e.message ) );
	page.on( 'console', ( m ) => 'error' === m.type() && errors.push( m.text() ) );
	await page.goto( base + '/', { waitUntil: 'networkidle' } );
	return { page, errors, context };
}

{
	const { page, errors, context } = await open( 1440 );
	await page.evaluate( () => window.scrollTo( 0, 900 ) );
	await page.waitForTimeout( 700 );
	const pinned = await page.evaluate( () => {
		const h = document.querySelector( '.creative-agency-header' );
		const s = getComputedStyle( h );
		return { stuck: h.classList.contains( 'is-stuck' ), pos: s.position, bg: s.backgroundColor, top: Math.round( h.getBoundingClientRect().top ) };
	} );
	ok( 'header pins past 400px', pinned.stuck && 'fixed' === pinned.pos && 0 === pinned.top, JSON.stringify( pinned ) );

	const count = await page.$( '.creative-agency-count' );
	await count.scrollIntoViewIfNeeded();
	const early = await count.evaluate( ( el ) => el.textContent );
	await page.waitForTimeout( 2200 );
	const late = await count.evaluate( ( el ) => el.textContent + ' / ' + el.getAttribute( 'aria-label' ) );
	ok( 'figure counts up to its value', late.startsWith( late.split( ' / ' )[ 1 ] ) && early !== late.split( ' / ' )[ 0 ], `${ early } → ${ late }` );

	await page.evaluate( () => window.scrollTo( 0, 0 ) );
	await page.click( '.creative-agency-play a' );
	await page.waitForTimeout( 500 );
	const film = await page.evaluate( () => {
		const d = document.querySelector( '.creative-agency-video-dialog' );
		return d ? { open: d.open, src: d.querySelector( 'iframe' ).src } : null;
	} );
	ok( 'play button opens the film', film && film.open && film.src.includes( 'youtube-nocookie.com/embed/' ), JSON.stringify( film ) );
	await page.keyboard.press( 'Escape' );
	await page.waitForTimeout( 300 );
	const closed = await page.evaluate( () => {
		const d = document.querySelector( '.creative-agency-video-dialog' );
		return { open: d.open, src: d.querySelector( 'iframe' ).src };
	} );
	ok( 'Escape closes it and stops the video', ! closed.open && 'about:blank' === closed.src );

	await page.click( '.creative-agency-scheme-toggle a' );
	await page.waitForTimeout( 300 );
	const dark = await page.evaluate( () => ( {
		dark: document.documentElement.classList.contains( 'creative-agency-dark' ),
		pressed: document.querySelector( '.creative-agency-scheme-toggle a' ).getAttribute( 'aria-pressed' ),
		bg: getComputedStyle( document.body ).backgroundColor,
	} ) );
	ok( 'dark mode switch', dark.dark && 'true' === dark.pressed, JSON.stringify( dark ) );
	ok( 'no console errors (desktop)', ! errors.length, errors.join( ' | ' ).slice( 0, 300 ) );
	await context.close();
}

{
	const { page, errors, context } = await open( 390 );
	const btn = await page.$( '.creative-agency-header .wp-block-navigation__responsive-container-open' );
	ok( 'menu button shown at 390px', !! btn && await btn.isVisible() );
	if ( btn ) {
		await btn.click();
		await page.waitForTimeout( 500 );
		const menu = await page.evaluate( () => {
			const m = document.querySelector( '.creative-agency-header .wp-block-navigation__responsive-container' );
			return { open: m.classList.contains( 'is-menu-open' ), links: [ ...m.querySelectorAll( 'a' ) ].filter( ( a ) => a.offsetWidth ).length };
		} );
		ok( 'menu opens with its links', menu.open && menu.links >= 6, JSON.stringify( menu ) );
	}
	const overflow = await page.evaluate( () => document.documentElement.scrollWidth - window.innerWidth );
	ok( 'no horizontal scroll at 390px', overflow <= 0, `${ overflow }px` );
	ok( 'no console errors (phone)', ! errors.length, errors.join( ' | ' ).slice( 0, 300 ) );
	await context.close();
}

await browser.close();
console.log( results.join( '\n' ) );
process.exit( results.some( ( r ) => r.startsWith( 'FAIL' ) ) ? 1 : 0 );
