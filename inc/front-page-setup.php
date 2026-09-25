<?php
/**
 * Give a fresh install the site it was shown in the screenshot.
 *
 * A block theme activated on an empty site shows the blog index, which looks
 * nothing like the demo and leaves the owner to assemble a home page from
 * patterns before they can tell whether they like it. This builds the pages
 * once, on first activation, and never touches them again.
 *
 * Patterns are **expanded into real post content** rather than referenced, so
 * every word is editable in the editor without hunting through theme files.
 * That expansion is also why the dynamic parts of the theme are shortcodes:
 * PHP inside stored post content never runs, so a pattern that rendered the
 * contact form inline would freeze its output into the page permanently.
 * `[creative_agency_contact_form]` survives the round trip because a shortcode is
 * expanded at render time, every time.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;

const CREATIVE_AGENCY_SETUP_FLAG = 'creative_agency_front_page_created';

/**
 * Pages to create, in order. Path => [title, pattern, template].
 *
 * A path with a slash is a child page: the four projects live under Work, so
 * their addresses read /work/nookdesk/ and the Work page is their parent.
 *
 * @return array<string, array<string, string>>
 */
function creative_agency_starter_pages() {
	return array(
		'home'                 => array(
			'title'    => __( 'Home', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-home',
			'template' => 'page-no-title',
		),
		'about'                => array(
			'title'    => __( 'About', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-about',
			'template' => 'page-no-title',
		),
		'work'                 => array(
			'title'    => __( 'Work', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-work',
			'template' => 'page-no-title',
		),
		'work/nookdesk'        => array(
			'title'    => __( 'Nookdesk booking app', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-project-nookdesk',
			'template' => 'page-no-title',
		),
		'work/whisk'           => array(
			'title'    => __( 'Whisk design system', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-project-whisk',
			'template' => 'page-no-title',
		),
		'work/harrow-and-pine' => array(
			'title'    => __( 'Harrow & Pine packaging', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-project-harrow-and-pine',
			'template' => 'page-no-title',
		),
		'work/arcade-club'     => array(
			'title'    => __( 'Arcade Club app', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-project-arcade-club',
			'template' => 'page-no-title',
		),
		'services'             => array(
			'title'    => __( 'Services', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-services',
			'template' => 'page-no-title',
		),
		'blog'                 => array(
			'title'    => __( 'Blog', 'creative-agency' ),
			'pattern'  => '',
			'template' => '',
		),
		'contact'              => array(
			'title'    => __( 'Contact', 'creative-agency' ),
			'pattern'  => 'creative-agency/page-contact',
			'template' => 'page-no-title',
		),
	);
}


/**
 * Build the starter site, once.
 *
 * Guarded three ways: a one-shot option, a check that this is a genuinely
 * fresh site, and a per-page check that the slug is free. Activating, trying
 * another theme and coming back must not produce a second set of pages or
 * overwrite the first.
 */
function creative_agency_create_front_page() {
	if ( get_option( CREATIVE_AGENCY_SETUP_FLAG ) ) {
		return;
	}

	// Only on a site that has not been built yet: someone activating Creative Agency
	// over an existing site wants their pages left alone. WordPress's own two
	// pages do not count as "built" — a brand new install has them.
	$existing = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 5,
			'fields'         => 'ids',
			'exclude'        => array_filter(
				array(
					(int) get_option( 'wp_page_for_privacy_policy' ),
					(int) ( get_page_by_path( 'sample-page', OBJECT, array( 'page' ) )->ID ?? 0 ),
				)
			),
		)
	);

	if ( count( $existing ) > 1 ) {
		update_option( CREATIVE_AGENCY_SETUP_FLAG, 'skipped: site already had pages', false );
		return;
	}

	$created = array();

	// The content is the theme's own pattern markup, not user input, and it
	// carries one thing kses would strip for a user without unfiltered_html —
	// the contact page's map, an <iframe>. A site administrator on a multisite
	// network is such a user, so without this the contact page would be built
	// with no map. The filters are put back as soon as the pages exist.
	$kses = false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' );
	if ( $kses ) {
		kses_remove_filters();
	}

	// get_page_by_path() is always given the post type as an ARRAY: with a
	// string it also searches attachments, so an image uploaded as
	// "contact.jpg" would count as the Contact page and it would never be made.
	foreach ( creative_agency_starter_pages() as $path => $page ) {
		if ( get_page_by_path( $path, OBJECT, array( 'page' ) ) ) {
			continue;
		}

		$parent = 0;
		$slug   = $path;
		if ( false !== strpos( $path, '/' ) ) {
			list( $parent_path, $slug ) = explode( '/', $path, 2 );
			$parent                     = isset( $created[ $parent_path ] ) ? $created[ $parent_path ] : 0;
			if ( ! $parent ) {
				$existing_parent = get_page_by_path( $parent_path, OBJECT, array( 'page' ) );
				$parent          = $existing_parent ? $existing_parent->ID : 0;
			}
		}

		$content = '';
		if ( $page['pattern'] ) {
			$content = creative_agency_pattern_content( $page['pattern'] );
			if ( '' === $content ) {
				continue;
			}
		}

		// wp_insert_post() unslashes its input. Pattern markup carries JSON escapes
		// such as \u002d in block attributes; without wp_slash() they lose their
		// backslash and every spacer opens as "unexpected or invalid content".
		$id = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_content' => wp_slash( $content ),
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_parent'  => $parent,
				'menu_order'   => count( $created ),
			)
		);

		if ( ! is_wp_error( $id ) && $id ) {
			$created[ $path ] = $id;
			if ( $page['template'] ) {
				update_post_meta( $id, '_wp_page_template', $page['template'] );
			}
		}
	}

	if ( $kses ) {
		kses_init_filters();
	}

	if ( isset( $created['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created['home'] );
	}
	if ( isset( $created['blog'] ) ) {
		update_option( 'page_for_posts', $created['blog'] );
	}

	creative_agency_link_starter_pages( $created );
	creative_agency_create_primary_menu( $created );

	// Claimed only now, and only if something was actually built. Firing before
	// the pattern registry is ready is a real possibility — the content comes
	// back empty and every page is skipped — and a flag set up front would make
	// that one bad moment permanent. Left unset, the admin_init retry below
	// finishes the job on the next page load.
	if ( $created ) {
		update_option( CREATIVE_AGENCY_SETUP_FLAG, gmdate( 'c' ), false );
	}
}
add_action( 'after_switch_theme', 'creative_agency_create_front_page' );

/**
 * Second chance.
 *
 * after_switch_theme can fire before the block pattern registry is populated,
 * in which case every page resolves to empty content and nothing is built. This
 * runs once more on the first admin request, by which time patterns are
 * certainly registered, and does nothing at all once the flag is set.
 */
function creative_agency_create_front_page_retry() {
	if ( get_option( CREATIVE_AGENCY_SETUP_FLAG ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	creative_agency_create_front_page();
}
add_action( 'admin_init', 'creative_agency_create_front_page_retry' );

/**
 * The markup of a registered pattern, with nested pattern references expanded.
 *
 * The page patterns are built out of `<!-- wp:pattern {"slug":"..."} /-->`
 * references. Stored in a post those still *render* — WordPress resolves them
 * on output — but they are not editable: the editor shows one opaque block per
 * section, and changing a word means finding the pattern file in the theme.
 * The whole point of building the starter site as real content is that the
 * owner can rewrite it, so the references are resolved here, recursively.
 *
 * `$seen` guards against a pattern that references itself, directly or through
 * a chain. Without it that is an infinite loop and a white screen, at
 * activation, on someone else's site.
 *
 * @param string   $name Pattern name, e.g. `creative-agency/page-home`.
 * @param string[] $seen Names already being expanded on this branch.
 * @return string Pattern content, or '' when it is not registered.
 */
function creative_agency_pattern_content( $name, $seen = array() ) {
	if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return '';
	}

	$registry = WP_Block_Patterns_Registry::get_instance();
	if ( ! $registry->is_registered( $name ) ) {
		return '';
	}

	$pattern = $registry->get_registered( $name );
	$content = isset( $pattern['content'] ) ? $pattern['content'] : '';

	if ( '' === $content || in_array( $name, $seen, true ) ) {
		return $content;
	}

	$seen[] = $name;

	return (string) preg_replace_callback(
		'#<!--\s*wp:pattern\s+(\{.*?\})\s*/-->#s',
		static function ( $matches ) use ( $seen ) {
			$attributes = json_decode( $matches[1], true );

			if ( ! is_array( $attributes ) || empty( $attributes['slug'] ) ) {
				return $matches[0];
			}

			$nested = creative_agency_pattern_content( $attributes['slug'], $seen );

			// A reference we cannot resolve is left as it was: it still
			// renders, which is better than deleting the section.
			return '' === $nested ? $matches[0] : $nested;
		},
		$content
	);
}

/**
 * Point the starter pages' internal links at the pages that now exist.
 *
 * The patterns write their links as home_url( '/work/' ) and so on, which is
 * right on a site with pretty permalinks and wrong on one without them, or on a
 * site where a slug was taken and WordPress gave the page `work-2`. Each link
 * to a page this run created is swapped for that page's real permalink. Longer
 * paths go first, so /work/ never rewrites the front of /work/nookdesk/.
 *
 * @param array<string, int> $pages Path => page ID.
 */
function creative_agency_link_starter_pages( $pages ) {
	if ( ! $pages ) {
		return;
	}

	$map = array();
	foreach ( $pages as $path => $id ) {
		if ( 'home' === $path ) {
			continue;
		}
		$from = esc_url( home_url( '/' . $path . '/' ) );
		$to   = esc_url( get_permalink( $id ) );
		if ( $from && $to && $from !== $to ) {
			$map[ $from ] = $to;
		}
	}

	if ( ! $map ) {
		return;
	}

	uksort(
		$map,
		static function ( $a, $b ) {
			return strlen( $b ) - strlen( $a );
		}
	);

	foreach ( $pages as $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		$content = strtr( $post->post_content, $map );
		if ( $content !== $post->post_content ) {
			wp_update_post(
				array(
					'ID'           => $id,
					'post_content' => wp_slash( $content ),
				)
			);
		}
	}
}

/**
 * A navigation menu pointing at the pages just created.
 *
 * Block themes use a `wp_navigation` post rather than a nav menu, and the
 * header falls back to a page list when there is none — so this is an
 * improvement on the fallback, not a requirement for the header to work. The
 * four projects hang under Work, as the design's own "Pages" menu does.
 *
 * @param array<string, int> $pages Path => page ID.
 */
function creative_agency_create_primary_menu( $pages ) {
	if ( ! $pages ) {
		return;
	}

	$link = static function ( $id ) {
		return sprintf(
			'<!-- wp:navigation-link {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type"} /-->',
			esc_attr( get_the_title( $id ) ),
			$id,
			esc_url( get_permalink( $id ) )
		);
	};

	$items = '';
	foreach ( array( 'home', 'about', 'work', 'services', 'blog', 'contact' ) as $path ) {
		if ( ! isset( $pages[ $path ] ) ) {
			continue;
		}

		$id = $pages[ $path ];

		// core/home-link rather than a custom link for the front page: only
		// home-link is given `current-menu-item`, so a custom link would never
		// highlight while someone is actually on the home page.
		if ( 'home' === $path ) {
			$items .= '<!-- wp:home-link {"label":"' . esc_attr( get_the_title( $id ) ) . '"} /-->';
			continue;
		}

		$children = '';
		foreach ( $pages as $child_path => $child_id ) {
			if ( 0 === strpos( $child_path, $path . '/' ) ) {
				$children .= $link( $child_id );
			}
		}

		if ( '' === $children ) {
			$items .= $link( $id );
			continue;
		}

		$items .= sprintf(
			'<!-- wp:navigation-submenu {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type"} -->%s<!-- /wp:navigation-submenu -->',
			esc_attr( get_the_title( $id ) ),
			$id,
			esc_url( get_permalink( $id ) ),
			$children
		);
	}

	if ( '' === $items ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'   => __( 'Primary', 'creative-agency' ),
			'post_name'    => 'primary',
			'post_content' => wp_slash( $items ),
			'post_status'  => 'publish',
			'post_type'    => 'wp_navigation',
		)
	);
}
