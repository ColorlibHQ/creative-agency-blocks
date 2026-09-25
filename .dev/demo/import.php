<?php
/**
 * Creative Agency demo content: everything the live demo shows beyond what the
 * theme itself builds on activation.
 *
 * Run on the demo subsite, as the PHP user, with this directory (import.php and
 * media/) copied somewhere the PHP user can read:
 *
 *   sudo -u www-data wp --path=/var/www/colorlibhub.com/public \
 *     --url=https://colorlibhub.com/creative-agency-blocks/ \
 *     eval "require '/path/to/demo/import.php';"
 *
 * `wp eval` + `require`, not `wp eval-file`: eval-file runs the file inside a
 * function, where globals are null. Nothing here reads a global anyway (the
 * work is done in a class), so both would behave, but keep to the documented
 * command.
 *
 * What it does, in order:
 *  1. Refuses to run unless Creative Agency is the active theme.
 *  2. Picks the author (user 1 when it belongs to this site, otherwise the
 *     site's first administrator; CREATIVE_AGENCY_DEMO_AUTHOR=<login> overrides).
 *  3. Sets the site title and tagline.
 *  4. Makes sure the theme's starter pages and menu exist by calling the theme's
 *     own creative_agency_create_front_page() — it never creates pages itself,
 *     and that function does nothing once it has run.
 *  5. Deletes WordPress's "Hello world!" post and "Sample Page".
 *  6. Six journal posts with categories, tags, excerpts and featured images
 *     (media/ next to this file, sideloaded into the media library — demo
 *     photos never go in the theme zip), plus one comment and a reply.
 *
 * Idempotent: posts are found by slug with get_posts( name + post_type ), never
 * get_page_by_path() (which also matches attachments); media and comments are
 * found by a marker meta key. A second run updates the posts' copy in place
 * and adds nothing. Everything given to wp_insert_post()/wp_update_post()/
 * wp_insert_comment() goes through wp_slash(), because they unslash.
 *
 * It sends no mail (wp_insert_comment() does not notify) and makes no remote
 * requests.
 *
 * Photographs in media/ (Unsplash License, https://unsplash.com/license):
 *  - design-system-library.jpg  Daniel Korpai  https://unsplash.com/photos/SqNCiNJCa3E
 *  - prototype-in-hand.jpg      Daniel Korpai  https://unsplash.com/photos/bOKIptPzdPk
 *  - queue-app-lock-screen.jpg  David Švihovec https://unsplash.com/photos/e-AB_mUpCK8
 *  - apothecary-dropper-bottle.jpg Avtar Singh https://unsplash.com/photos/MkuwnDkT1s0
 *  - studio-research-meeting.jpg  Jason Goodman  unsplash.com/photos/MUZFKa_mttU
 *    (withdrawn from Unsplash since; archived with its licence at
 *    https://web.archive.org/web/20220811094218/https://unsplash.com/photos/MUZFKa_mttU)
 *  - studio-corner-desk.jpg     Elvis, Pexels License
 *    https://www.pexels.com/photo/photo-of-a-laptop-and-a-tablet-on-the-table-2528118/
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Creative_Agency_Demo_Import' ) ) {

	/**
	 * The importer. A class so a second require in the same process cannot
	 * redeclare anything, and so nothing depends on variable scope.
	 */
	final class Creative_Agency_Demo_Import {

		/** Marker meta key on everything this script creates. */
		const MARK = '_creative_agency_demo';

		/** @var string Directory holding this file and media/. */
		private $dir;

		/** @var int Author of the posts and the reply. */
		private $author = 0;

		/** @var array<string, int> Counts for the summary. */
		private $done = array(
			'posts created' => 0,
			'posts updated' => 0,
			'media added'   => 0,
			'comments'      => 0,
			'removed'       => 0,
		);

		/**
		 * @param string $dir Directory holding this file.
		 */
		public function __construct( $dir ) {
			$this->dir = rtrim( $dir, '/' );
		}

		/** A line of output, through WP-CLI when it is there. */
		private static function say( $line ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::log( $line );
			} else {
				echo esc_html( $line ) . "\n";
			}
		}

		/** Stop with an error. */
		private static function fail( $line ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::error( $line );
			}
			self::say( 'Error: ' . $line );
			exit( 1 );
		}

		/** Run every step. */
		public function run() {
			if ( 'creative-agency' !== get_template() || ! function_exists( 'creative_agency_create_front_page' ) ) {
				self::fail( 'Creative Agency is not the active theme on ' . home_url( '/' ) . '. Activate it first.' );
			}

			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$this->pick_author();
			$this->site_identity();
			$this->starter_pages();
			$this->remove_defaults();
			$this->posts();

			$pages = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
			self::say( 'Pages: ' . count( $pages ) . ' published, front page #' . (int) get_option( 'page_on_front' ) . '.' );

			$summary = array();
			foreach ( $this->done as $what => $n ) {
				$summary[] = $n . ' ' . $what;
			}
			self::say( 'Done: ' . implode( ', ', $summary ) . '.' );
		}

		/** The user who writes the journal. */
		private function pick_author() {
			$user  = null;
			$login = getenv( 'CREATIVE_AGENCY_DEMO_AUTHOR' );

			if ( $login ) {
				$user = get_user_by( 'login', $login );
			}
			if ( ! $user ) {
				$first = get_user_by( 'id', 1 );
				if ( $first && ( ! is_multisite() || is_user_member_of_blog( $first->ID ) ) ) {
					$user = $first;
				}
			}
			if ( ! $user ) {
				$admins = get_users(
					array(
						'role'    => 'administrator',
						'number'  => 1,
						'orderby' => 'ID',
					)
				);
				$user   = $admins ? $admins[0] : null;
			}
			if ( ! $user && is_multisite() ) {
				foreach ( get_super_admins() as $super ) {
					$user = get_user_by( 'login', $super );
					if ( $user ) {
						break;
					}
				}
			}
			if ( ! $user ) {
				self::fail( 'No administrator found to author the posts.' );
			}

			$this->author = (int) $user->ID;
			wp_set_current_user( $this->author );
			self::say( 'Author: ' . $user->user_login . ' (#' . $this->author . ').' );
		}

		/** Site title and tagline: the brand tile shows the title's first letter. */
		private function site_identity() {
			update_option( 'blogname', 'Creative Agency' );
			update_option( 'blogdescription', 'Design and development studio' );
		}

		/** The theme's own starter pages and menu, via the theme's own function. */
		private function starter_pages() {
			if ( ! get_option( CREATIVE_AGENCY_SETUP_FLAG ) ) {
				creative_agency_create_front_page();
			}

			$flag = (string) get_option( CREATIVE_AGENCY_SETUP_FLAG );
			if ( 0 === strpos( $flag, 'skipped' ) ) {
				self::say( 'Warning: the theme did not build its pages (' . $flag . '). The journal is imported anyway.' );
				return;
			}

		}

		/** WordPress's own "Hello world!" post and "Sample Page", when they are still the defaults. */
		private function remove_defaults() {
			$defaults = array(
				array( 'hello-world', 'post', 'Hello world!' ),
				array( 'sample-page', 'page', 'Sample Page' ),
			);
			foreach ( $defaults as $default ) {
				list( $name, $type, $title ) = $default;
				$found = get_posts(
					array(
						'name'           => $name,
						'post_type'      => $type,
						'post_status'    => 'any',
						'posts_per_page' => -1,
					)
				);
				foreach ( $found as $post ) {
					if ( $title === $post->post_title ) {
						wp_delete_post( $post->ID, true );
						++$this->done['removed'];
					}
				}
			}
		}

		/** A category's ID, created when missing. */
		private function category( $name ) {
			$term = term_exists( $name, 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $name, 'category' );
			}
			return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
		}

		/** An attachment for a file in media/, uploaded once, titled after its post. */
		private function media( $file, $alt, $parent, $title ) {
			$found = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => self::MARK, // phpcs:ignore WordPress.DB.SlowDBQuery -- six posts, once.
					'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			);
			if ( $found ) {
				update_post_meta( $found[0], '_wp_attachment_image_alt', wp_slash( $alt ) );
				return (int) $found[0];
			}

			$source = $this->dir . '/media/' . $file;
			if ( ! is_readable( $source ) ) {
				self::say( 'Warning: missing ' . $source );
				return 0;
			}

			// media_handle_sideload() MOVES the file it is given; hand it a copy.
			$tmp = wp_tempnam( $file );
			copy( $source, $tmp );
			$id = media_handle_sideload(
				array(
					'name'     => $file,
					'tmp_name' => $tmp,
				),
				$parent,
				$title
			);
			if ( is_wp_error( $id ) ) {
				wp_delete_file( $tmp );
				self::say( 'Warning: ' . $file . ': ' . $id->get_error_message() );
				return 0;
			}

			update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
			update_post_meta( $id, self::MARK, $file );
			++$this->done['media added'];
			return (int) $id;
		}

		/** Block markup helpers. */
		private static function p( $text ) {
			return '<!-- wp:paragraph -->' . "\n" . '<p>' . $text . '</p>' . "\n" . '<!-- /wp:paragraph -->';
		}

		private static function h( $text ) {
			return '<!-- wp:heading -->' . "\n" . '<h2 class="wp-block-heading">' . $text . '</h2>' . "\n" . '<!-- /wp:heading -->';
		}

		private static function quote( $text, $cite ) {
			return '<!-- wp:quote -->' . "\n" . '<blockquote class="wp-block-quote">' . self::p( $text ) . '<cite>' . $cite . '</cite></blockquote>' . "\n" . '<!-- /wp:quote -->';
		}

		private static function items( $items ) {
			$lis = '';
			foreach ( $items as $item ) {
				$lis .= '<!-- wp:list-item -->' . "\n" . '<li>' . $item . '</li>' . "\n" . '<!-- /wp:list-item -->' . "\n";
			}
			return '<!-- wp:list -->' . "\n" . '<ul class="wp-block-list">' . $lis . '</ul>' . "\n" . '<!-- /wp:list -->';
		}

		/** The journal. Newest first; `days` is how long ago it was published. */
		private function journal() {
			return array(
				array(
					'slug'     => 'shipping-a-design-system-in-six-weeks',
					'title'    => 'What we learned shipping a design system in six weeks',
					'category' => 'Design systems',
					'tags'     => array( 'design systems', 'process' ),
					'days'     => 4,
					'media'    => 'design-system-library.jpg',
					'alt'      => 'A designer at a large monitor showing a grid of hundreds of small image thumbnails',
					'excerpt'  => 'Whisk had 214 components and six hundred recipes. Six weeks later it had 48 components, one set of tokens and a team that could say no to a new button.',
					'body'     => array(
						self::p( 'When Whisk asked us for a design system, they had 214 components in their design file and no two screens that agreed on the size of a button. Six hundred recipes, three platforms, and a component for every one-off screen anyone had ever needed.' ),
						self::p( 'We gave ourselves six weeks, and spent the first one not designing anything. We printed every screen in the app, laid them on the studio floor, and circled every element that did the same job as another one. By Friday the floor was mostly red.' ),
						self::h( 'Cut first, name second' ),
						self::p( 'The temptation with a design system is to start with the tokens: colours, type sizes, spacing. We started with the cuts instead. Every component had to earn its place by appearing on at least three screens, or by doing something nothing else could.' ),
						self::items(
							array(
								'214 components became 48, each with a usage note and a code example.',
								'Nine greys became four, and every one of them passes contrast on white.',
								'Spacing moved to a scale of eight steps, and the one-off 13px margin went for good.',
							)
						),
						self::quote( 'The system is not the file. The system is the conversation the file lets designers and developers have without us in the room.', 'Maya Okafor, design lead' ),
						self::p( 'Weeks two to five were the unglamorous part: rebuilding each surviving component in code alongside the design file, so neither could drift from the other. The last week was handover. Whisk\'s own team now runs the contribution process, and the last time we checked, the count was still 48.' ),
					),
				),
				array(
					'slug'     => 'twenty-two-interviews-before-a-wireframe',
					'title'    => 'Twenty-two interviews before a single wireframe',
					'category' => 'Research',
					'tags'     => array( 'research', 'process' ),
					'days'     => 13,
					'media'    => 'studio-research-meeting.jpg',
					'alt'      => 'The team around a long white table, looking at a monitor on a wall of sticky notes',
					'excerpt'  => 'For Nookdesk we spent two weeks talking to members and the front-desk team before anyone opened a design tool. Here is what that bought us.',
					'body'     => array(
						self::p( 'Nookdesk came to us with a clear request: an app for booking desks. It would have been easy to start drawing screens that afternoon. Instead we spent two weeks asking members how they actually decided where to sit.' ),
						self::p( 'Twenty-two interviews, a week behind the front desk, and one very long spreadsheet of Monday-morning bookings later, the brief looked different. Nobody wanted to book a desk. They wanted to know, on the way in, where they could sit today and who else would be there.' ),
						self::h( 'What the interviews changed' ),
						self::p( 'The first version we had imagined had a calendar at its centre. The one we built has a live floor plan, and booking takes two taps from it. The calendar is still there, one screen down, for the few people who plan a week ahead.' ),
						self::quote( 'People told us they hated booking. What they hated was arriving to find their usual desk gone.', 'Jonas Albrecht, researcher' ),
						self::p( 'Research does not always move a project this far. But two weeks of listening cost less than a single sprint spent building the wrong home screen, and no-shows at Nookdesk fell by a third in the first quarter after launch.' ),
					),
				),
				array(
					'slug'     => 'designing-a-label-for-a-steamy-bathroom',
					'title'    => 'Designing a label that survives a steamy bathroom',
					'category' => 'Packaging',
					'tags'     => array( 'packaging', 'print' ),
					'days'     => 22,
					'media'    => 'apothecary-dropper-bottle.jpg',
					'alt'      => 'An amber glass dropper bottle with a blank white label, standing on a stone block',
					'excerpt'  => 'Harrow & Pine\'s face oil lives next to a hot shower. The label had to look quiet on a shop shelf and still be readable after three months of steam.',
					'body'     => array(
						self::p( 'Harrow &amp; Pine distil their own pine and cedar oils in a barn outside Mendocino. When they asked us to package their first face oil, the brief fitted on one line: it should look as considered as what is inside it.' ),
						self::p( 'The harder brief was the one nobody wrote down. A face oil lives on a bathroom shelf, next to a hot shower, handled with wet fingers twice a day. Most beautiful uncoated papers turn to felt within a month in that room.' ),
						self::h( 'Testing in the wrong room' ),
						self::p( 'We printed the label on eleven stocks and stuck them to bottles in the studio shower for six weeks. Four survived. Of those, one still took the letterpress impression we wanted without the ink bleeding at the edges.' ),
						self::items(
							array(
								'The amber glass and black dropper stayed: they protect the oil and they already looked right.',
								'The type came from the founders\' hand-written batch notes, redrawn so it holds up at six points.',
								'The carton, shipper and refill pouch share one board and one ink, so a reorder is a single print run.',
							)
						),
						self::p( 'The finished label is almost empty, which was the point. On a shelf crowded with louder bottles, the quiet one is the one people pick up.' ),
					),
				),
				array(
					'slug'     => 'prototypes-you-can-hold-on-day-three',
					'title'    => 'Prototypes you can hold on day three',
					'category' => 'Studio',
					'tags'     => array( 'studio', 'prototyping', 'development' ),
					'days'     => 31,
					'media'    => 'prototype-in-hand.jpg',
					'alt'      => 'A hand holding a phone with a travel app open, over a laptop showing the same screens',
					'excerpt'  => 'Our developers sit in on the first workshop, and by the third day there is something running on a real phone. Here is why we work that way.',
					'body'     => array(
						self::p( 'On most of our projects, a developer is in the room for the very first workshop. Not to take notes on a feature list, but to hear the problem the way the client describes it, before anyone has turned it into screens.' ),
						self::p( 'It pays off quickly. By the end of the third day there is usually a rough prototype running on a real phone: ugly, half-connected, and far more useful than a polished mock-up, because people hold it the way they will hold the finished thing.' ),
						self::h( 'Why a phone beats a slide' ),
						self::p( 'A picture of an app on a slide always looks finished. The same screen on a phone, in someone\'s hand on a busy street, shows you at once that the button is too small, the text too long and the loading spinner too frequent. Those are cheap to fix on day three and expensive in month three.' ),
						self::quote( 'The first time a client holds the prototype, they stop talking about colours and start talking about their customers.', 'Sam Whitfield, lead developer' ),
						self::p( 'The prototype is thrown away, always. What survives is the list of things we learned from watching people use it, and a development team that already understands why every screen exists.' ),
					),
				),
				array(
					'slug'     => 'a-queue-app-for-forty-consoles',
					'title'    => 'A queue app for forty consoles and one whiteboard',
					'category' => 'Case notes',
					'tags'     => array( 'mobile', 'case study' ),
					'days'     => 45,
					'media'    => 'queue-app-lock-screen.jpg',
					'alt'      => 'A phone with a neon lock screen lying on a white games console',
					'excerpt'  => 'Arcade Club\'s queue lived on a whiteboard by the door. We replaced it with an app that tells you ten minutes before your turn.',
					'body'     => array(
						self::p( 'Arcade Club is a gaming lounge in downtown Oakland with forty consoles and a tournament every Friday. Until last year, the queue lived on a whiteboard by the door, and the most important skill in the building was guarding your place on it.' ),
						self::p( 'The club wanted an app. What they needed, it turned out, was the whiteboard in everyone\'s pocket: the same list, the same order, and a nudge ten minutes before your turn so you could leave the bar in time.' ),
						self::h( 'Small scope, on purpose' ),
						self::items(
							array(
								'A live queue that holds your place wherever you are in the building.',
								'Group bookings, so four friends can claim one console without four accounts.',
								'A lock-screen tournament bracket on Fridays, updated as each match ends.',
							)
						),
						self::p( 'We built it in React Native and launched on iOS and Android in the same week. The whiteboard is still by the door, now with the app\'s QR code on it, and the club\'s own developer has shipped three updates since we handed it over.' ),
					),
				),
				array(
					'slug'     => 'the-desk-we-keep-coming-back-to',
					'title'    => 'The desk we keep coming back to',
					'category' => 'Studio',
					'tags'     => array( 'studio' ),
					'days'     => 60,
					'media'    => 'studio-corner-desk.jpg',
					'alt'      => 'A white desk in a bright corner with a laptop, a tablet and a lamp, and a white chair pulled out',
					'excerpt'  => 'Every studio has one quiet corner. Ours has a lamp, a plant and a rule: no meetings at this desk.',
					'body'     => array(
						self::p( 'Our studio on Broadway is mostly long white tables and walls of sticky notes. It is a good room for workshops and a noisy one for everything else. So in the far corner there is one desk with a different rule: nobody holds a meeting there.' ),
						self::p( 'It is where the hard hour of a project happens. The hour where a layout finally clicks, or a bug that has taken two days finally shows itself. Anyone can sit there, and anyone who does is left alone.' ),
						self::h( 'What is on it' ),
						self::p( 'Not much, on purpose: a lamp for the afternoons, a laptop and a tablet side by side so a design and its code can be read together, and a plant that has survived four years of designers forgetting to water it.' ),
						self::quote( 'Half our best ideas started in the workshop. The other half were finished at that desk.', 'Maya Okafor, design lead' ),
						self::p( 'If you visit the studio, you are welcome to try it. Just do not book a call there.' ),
					),
				),
			);
		}

		/** Create or refresh each post, its terms, image and marker. */
		private function posts() {
			$ids = array();
			foreach ( $this->journal() as $entry ) {
				$category = $this->category( $entry['category'] );
				$content  = implode( "\n\n", $entry['body'] );
				$data     = array(
					'post_title'    => $entry['title'],
					'post_name'     => $entry['slug'],
					'post_content'  => $content,
					'post_excerpt'  => $entry['excerpt'],
					'post_status'   => 'publish',
					'post_type'     => 'post',
					'post_author'   => $this->author,
					'post_category' => $category ? array( $category ) : array(),
					'tags_input'    => $entry['tags'],
				);

				$existing = get_posts(
					array(
						'name'           => $entry['slug'],
						'post_type'      => 'post',
						'post_status'    => 'any',
						'posts_per_page' => 1,
						'fields'         => 'ids',
					)
				);

				if ( $existing ) {
					$data['ID'] = (int) $existing[0];
					$id         = wp_update_post( wp_slash( $data ), true );
					++$this->done['posts updated'];
				} else {
					$data['post_date'] = gmdate( 'Y-m-d 09:30:00', strtotime( '-' . $entry['days'] . ' days' ) );
					$id                = wp_insert_post( wp_slash( $data ), true );
					++$this->done['posts created'];
				}

				if ( is_wp_error( $id ) || ! $id ) {
					self::say( 'Warning: ' . $entry['slug'] . ': ' . ( is_wp_error( $id ) ? $id->get_error_message() : 'not saved' ) );
					continue;
				}

				update_post_meta( $id, self::MARK, $entry['slug'] );
				$image = $this->media( $entry['media'], $entry['alt'], $id, $entry['title'] );
				if ( $image ) {
					set_post_thumbnail( $id, $image );
				}
				$ids[ $entry['slug'] ] = (int) $id;
			}

			if ( isset( $ids['twenty-two-interviews-before-a-wireframe'] ) ) {
				$this->conversation( $ids['twenty-two-interviews-before-a-wireframe'] );
			}
		}

		/** One reader comment and the author's reply, so threaded comments show. */
		private function conversation( $post_id ) {
			$author = get_userdata( $this->author );
			$first  = $this->comment(
				'reader',
				array(
					'comment_post_ID'      => $post_id,
					'comment_author'       => 'Priya Raman',
					'comment_author_email' => 'priya@example.com',
					'comment_author_url'   => '',
					'comment_content'      => 'We did the same with a booking tool for a clinic and the answer was nearly identical: nobody wanted a calendar, they wanted to know if they could walk in. How did you recruit the members for the interviews?',
					'comment_approved'     => 1,
					'comment_date'         => gmdate( 'Y-m-d 14:05:00', strtotime( '-11 days' ) ),
				)
			);
			if ( $first ) {
				$this->comment(
					'reply',
					array(
						'comment_post_ID'      => $post_id,
						'comment_parent'       => $first,
						'user_id'              => $this->author,
						'comment_author'       => $author ? $author->display_name : '',
						'comment_author_email' => $author ? $author->user_email : '',
						'comment_author_url'   => '',
						'comment_content'      => 'Mostly at the front desk: a coffee on us for fifteen minutes of their time. The front-desk team told us who came in every day and who only came on Mondays, so we could talk to both.',
						'comment_approved'     => 1,
						'comment_date'         => gmdate( 'Y-m-d 16:40:00', strtotime( '-11 days' ) ),
					)
				);
			}
		}

		/** A comment, once, found again by its marker. */
		private function comment( $key, $data ) {
			$found = get_comments(
				array(
					'post_id'    => $data['comment_post_ID'],
					'meta_key'   => self::MARK, // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value' => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
					'number'     => 1,
					'fields'     => 'ids',
					'status'     => 'all',
				)
			);
			if ( $found ) {
				return (int) $found[0];
			}

			$id = wp_insert_comment( wp_slash( $data ) );
			if ( $id ) {
				add_comment_meta( $id, self::MARK, $key );
				++$this->done['comments'];
			}
			return (int) $id;
		}
	}
}

( new Creative_Agency_Demo_Import( __DIR__ ) )->run();
