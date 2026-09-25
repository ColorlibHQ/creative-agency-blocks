/**
 * Run the Theme Check plugin against the active theme and print its findings.
 * Needs a Playground with the BUILT theme mounted and theme-check installed
 * (see CLAUDE.md), because the working tree carries .dev/ tooling users never get.
 *
 *   WP_URL=http://127.0.0.1:9492 node .dev/theme-check.mjs
 */
import { chromium } from 'playwright';

const url = process.env.WP_URL || 'http://127.0.0.1:9492';
const browser = await chromium.launch();
const page = await ( await browser.newContext() ).newPage();
await page.goto( url + '/wp-login.php', { waitUntil: 'commit' } );
await page.fill( '#user_login', 'admin' );
await page.fill( '#user_pass', 'password' );
await page.click( '#wp-submit' );
await page.waitForFunction( () => 'complete' === document.readyState );
await page.goto( url + '/wp-admin/themes.php?page=themecheck', { waitUntil: 'domcontentloaded' } );
await page.selectOption( 'select[name="themename"]', 'creative-agency' ).catch( () => {} );
await Promise.all( [ page.waitForNavigation( { timeout: 180000 } ), page.click( 'input[type="submit"]' ) ] );
const text = await page.evaluate( () => {
	const box = document.querySelector( '.tc-box' ) || document.querySelector( '#wpbody-content' );
	return box.innerText;
} );
await browser.close();
const lines = text.split( '\n' ).map( ( l ) => l.trim() ).filter( Boolean );
const findings = lines.filter( ( l ) => /^(REQUIRED|WARNING|RECOMMENDED|INFO)/.test( l ) );
console.log( lines.filter( ( l ) => /Running|passed|tests/i.test( l ) ).slice( 0, 3 ).join( '\n' ) );
findings.forEach( ( f ) => console.log( '  ' + f.slice( 0, 400 ) ) );
const count = ( k ) => findings.filter( ( f ) => f.startsWith( k ) ).length;
console.log( `\nREQUIRED ${ count( 'REQUIRED' ) }, WARNING ${ count( 'WARNING' ) }, RECOMMENDED ${ count( 'RECOMMENDED' ) }, INFO ${ count( 'INFO' ) }` );
