/**
 * Submit the contact form the way a visitor does and report where it lands.
 *
 *   WP_URL=http://127.0.0.1:9492 node .dev/form-check.mjs
 *
 * Expected: every case comes back to /contact/ (not the front page) with the
 * form's notice; a filled honeypot reports "sent" without sending; a real
 * message is "sent", or "failed" where the host cannot send mail (Playground).
 */
import { chromium } from 'playwright';

const url = ( process.env.WP_URL || 'http://127.0.0.1:9492' ) + '/contact/';
const browser = await chromium.launch();
const cases = [
	[ 'complete message', { message: 'We need an app for our bakery.', name: 'Ana Ruiz', email: 'ana@example.com', subject: 'New app' } ],
	[ 'bad email', { message: 'Hello', name: 'Ana', email: 'not-an-email', subject: '' }, true ],
	[ 'honeypot filled', { message: 'Buy now', name: 'Bot', email: 'bot@example.com', subject: '', creative_agency_website: 'spam.example' } ],
];
for ( const [ label, fields, novalidate ] of cases ) {
	const context = await browser.newContext();
	await context.addCookies( [ { name: 'playground_auto_login_already_happened', value: '1', domain: new URL( url ).hostname, path: '/' } ] );
	const page = await context.newPage();
	await page.goto( url, { waitUntil: 'domcontentloaded' } );
	if ( novalidate ) {
		await page.evaluate( () => document.querySelector( '.creative-agency-contact' ).setAttribute( 'novalidate', '' ) );
	}
	for ( const [ name, value ] of Object.entries( fields ) ) {
		await page.evaluate( ( [ n, v ] ) => {
			document.querySelector( `.creative-agency-contact [name="${ n }"]` ).value = v;
		}, [ name, value ] );
	}
	await Promise.all( [ page.waitForNavigation(), page.click( '.creative-agency-contact button[type="submit"]' ) ] );
	const notice = await page.evaluate( () => document.querySelector( '.creative-agency-contact__notice' )?.innerText || '(no notice)' );
	const where = new URL( page.url() );
	console.log( `${ label.padEnd( 18 ) } → ${ where.pathname }${ where.search }  "${ notice.slice( 0, 70 ) }"` );
	await context.close();
}
await browser.close();
