<?php
/**
 * The contact form.
 *
 * A studio's site exists to start a conversation about a project, so Creative
 * Agency ships the form rather than requiring a plugin for the one thing a
 * visitor came to do.
 *
 * It is a **shortcode**, not inline PHP in the pattern. That is not a style
 * preference: inc/front-page-setup.php expands patterns into real post content
 * so the copy stays editable, and PHP inside stored post content never runs.
 * A pattern that rendered the form inline would freeze whatever it produced at
 * activation into the page forever.
 *
 * A theme must not create database tables or register a post type, so the
 * theme stores nothing. It validates, then hands the message to whoever wants
 * it:
 *
 *   - `creative_agency_contact_handlers` — return true from any handler to say
 *     the message has been dealt with, and the built-in email is skipped. This
 *     is where a CRM, a helpdesk or a webhook hooks in.
 *   - `creative_agency_contact_email_to` / `_subject` / `_body` — adjust the
 *     email the theme sends when nothing else claims the message.
 *   - `creative_agency_contact_fields` — add, remove or relabel fields.
 *
 * The form works with JavaScript off: it is a plain POST to the same URL,
 * answered with a redirect carrying the result.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;

const CREATIVE_AGENCY_CONTACT_ACTION = 'creative_agency_contact';

/**
 * The fields the form asks for, in the order the design sets them: the message
 * first and full width, then name and email side by side, then the subject.
 *
 * @return array<string, array<string, mixed>>
 */
function creative_agency_contact_fields() {
	$fields = array(
		'message' => array(
			'label'    => __( 'Tell us about the project', 'creative-agency' ),
			'type'     => 'textarea',
			'required' => true,
		),
		'name'    => array(
			'label'        => __( 'Your name', 'creative-agency' ),
			'type'         => 'text',
			'autocomplete' => 'name',
			'required'     => true,
		),
		'email'   => array(
			'label'        => __( 'Email address', 'creative-agency' ),
			'type'         => 'email',
			'autocomplete' => 'email',
			'required'     => true,
		),
		'subject' => array(
			'label'    => __( 'Subject', 'creative-agency' ),
			'type'     => 'text',
			'required' => false,
		),
	);

	/**
	 * Filters the contact form fields.
	 *
	 * @param array $fields Field definitions keyed by name.
	 */
	return apply_filters( 'creative_agency_contact_fields', $fields );
}

/**
 * Render one field, label included.
 *
 * Labels are real <label for> elements, always. A placeholder is not a label:
 * it is unreadable to some screen readers and it disappears the moment the
 * field has content.
 *
 * @param string $name  Field name.
 * @param array  $field Field definition.
 * @return string
 */
function creative_agency_contact_field( $name, $field ) {
	$id       = 'creative-agency-contact-' . $name;
	$required = ! empty( $field['required'] );

	$attributes = array(
		'id'    => $id,
		'name'  => $name,
		'class' => 'creative-agency-field__control',
	);

	if ( $required ) {
		$attributes['required'] = 'required';
	}
	if ( ! empty( $field['autocomplete'] ) ) {
		$attributes['autocomplete'] = $field['autocomplete'];
	}

	$out  = '<p class="creative-agency-field creative-agency-field--' . esc_attr( $name ) . '">';
	$out .= '<label class="creative-agency-field__label" for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] );
	if ( $required ) {
		$out .= ' <span class="creative-agency-field__required" aria-hidden="true">*</span>';
	}
	$out .= '</label>';

	if ( 'textarea' === $field['type'] ) {
		$attributes['rows'] = 6;
		$out               .= '<textarea' . creative_agency_attributes( $attributes ) . '></textarea>';
	} else {
		$attributes['type'] = $field['type'];
		$out               .= '<input' . creative_agency_attributes( $attributes ) . '>';
	}

	return $out . '</p>';
}

/**
 * Build an attribute string from a map, escaping every value.
 *
 * @param array $attributes Attribute map.
 * @return string
 */
function creative_agency_attributes( $attributes ) {
	$out = '';
	foreach ( $attributes as $key => $value ) {
		if ( '' === $value ) {
			continue;
		}
		$out .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}
	return $out;
}

/**
 * The contact form.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function creative_agency_contact_form( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'button' => __( 'Send message', 'creative-agency' ),
		),
		$atts,
		'creative_agency_contact_form'
	);

	$out  = '<form class="creative-agency-contact" method="post" action="' . esc_url( creative_agency_current_url() ) . '#creative-agency-contact">';
	$out .= '<div id="creative-agency-contact" class="creative-agency-contact__anchor"></div>';
	$out .= creative_agency_contact_notice();
	$out .= wp_nonce_field( CREATIVE_AGENCY_CONTACT_ACTION, 'creative_agency_contact_nonce', true, false );
	$out .= '<input type="hidden" name="action" value="' . esc_attr( CREATIVE_AGENCY_CONTACT_ACTION ) . '">';

	// The page to come back to, carried explicitly.
	//
	// wp_get_referer() cannot do this job: it returns false whenever the
	// referer matches the current request URI, which is always the case for a
	// form that posts to its own page. Falling back to home_url() then leaves
	// the visitor on the front page with a confirmation and no form in sight.
	// Validated with wp_validate_redirect() on the way back out, so a crafted
	// value cannot send anyone off-site.
	$out .= '<input type="hidden" name="creative_agency_redirect" value="' . esc_url( creative_agency_current_url() ) . '">';

	// A field no visitor sees and no visitor fills in. Bots fill everything.
	$out .= '<p class="creative-agency-contact__trap" aria-hidden="true">';
	$out .= '<label for="creative-agency-contact-website">' . esc_html__( 'Leave this field empty', 'creative-agency' ) . '</label>';
	$out .= '<input id="creative-agency-contact-website" type="text" name="creative_agency_website" tabindex="-1" autocomplete="off">';
	$out .= '</p>';

	$out .= '<div class="creative-agency-contact__grid">';
	foreach ( creative_agency_contact_fields() as $name => $field ) {
		$out .= creative_agency_contact_field( $name, $field );
	}
	$out .= '</div>';

	$out .= '<p class="creative-agency-contact__actions">';
	$out .= '<button type="submit" class="wp-block-button__link wp-element-button">' . esc_html( $atts['button'] ) . '</button>';
	$out .= '</p>';

	$out .= '</form>';

	return $out;
}
add_shortcode( 'creative_agency_contact_form', 'creative_agency_contact_form' );

/**
 * The current URL, without any previous result parameter.
 *
 * @return string
 */
function creative_agency_current_url() {
	$permalink = get_permalink();
	if ( ! $permalink ) {
		$permalink = home_url( '/' );
	}
	return remove_query_arg( array( 'creative-agency-contact' ), $permalink );
}

/**
 * The message shown after a submission, if there is one.
 *
 * @return string
 */
function creative_agency_contact_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$result = isset( $_GET['creative-agency-contact'] ) ? sanitize_key( wp_unslash( $_GET['creative-agency-contact'] ) ) : '';

	$messages = array(
		'sent'    => array( 'ok', __( 'Thank you — your message is with us. We reply to every project brief within two working days.', 'creative-agency' ) ),
		'invalid' => array( 'error', __( 'Please check the form: every field marked * needs an answer.', 'creative-agency' ) ),
		'email'   => array( 'error', __( 'That email address does not look right.', 'creative-agency' ) ),
		'failed'  => array( 'error', __( 'Sorry, the message could not be sent. Please email or call us instead.', 'creative-agency' ) ),
		'expired' => array( 'error', __( 'That form had been open a while and expired. Please send it again.', 'creative-agency' ) ),
	);

	if ( ! isset( $messages[ $result ] ) ) {
		return '';
	}

	list( $kind, $text ) = $messages[ $result ];

	return '<p class="creative-agency-contact__notice is-' . esc_attr( $kind ) . '" role="status">' . esc_html( $text ) . '</p>';
}

/**
 * Handle a submitted message.
 *
 * Runs on `template_redirect` so it can redirect before anything is sent —
 * the POST/redirect/GET that stops a refresh from sending the message twice.
 */
function creative_agency_handle_contact() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked immediately below.
	if ( ! isset( $_POST['action'] ) || CREATIVE_AGENCY_CONTACT_ACTION !== $_POST['action'] ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce checked below; this only selects where to redirect.
	$posted   = isset( $_POST['creative_agency_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['creative_agency_redirect'] ) ) : '';
	$redirect = wp_validate_redirect( $posted, home_url( '/' ) );
	$redirect = remove_query_arg( array( 'creative-agency-contact' ), $redirect );

	$nonce = isset( $_POST['creative_agency_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['creative_agency_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, CREATIVE_AGENCY_CONTACT_ACTION ) ) {
		creative_agency_contact_redirect( $redirect, 'expired' );
	}

	// Silently accept and discard anything that filled the honeypot: telling a
	// bot it failed only teaches it to try again differently.
	if ( ! empty( $_POST['creative_agency_website'] ) ) {
		creative_agency_contact_redirect( $redirect, 'sent' );
	}

	$message = array();
	foreach ( creative_agency_contact_fields() as $name => $field ) {
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
		$raw = is_string( $raw ) ? $raw : '';

		if ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $raw );
		} elseif ( 'email' === $field['type'] ) {
			$value = sanitize_email( $raw );
			// sanitize_email() turns a malformed address into '', which would
			// then be reported as a missing answer. Say what is actually wrong.
			if ( '' !== trim( $raw ) && ! is_email( $value ) ) {
				creative_agency_contact_redirect( $redirect, 'email' );
			}
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( ! empty( $field['required'] ) && '' === $value ) {
			creative_agency_contact_redirect( $redirect, 'invalid' );
		}

		$message[ $name ] = $value;
	}

	if ( ! empty( $message['email'] ) && ! is_email( $message['email'] ) ) {
		creative_agency_contact_redirect( $redirect, 'email' );
	}

	/**
	 * Filters whether the message has already been handled.
	 *
	 * Return true from any handler and the theme will not send its own email —
	 * which is how a form plugin, a CRM or a webhook takes this over.
	 *
	 * @param bool  $handled Whether something has dealt with the message.
	 * @param array $message The sanitised message.
	 */
	$handled = apply_filters( 'creative_agency_contact_handlers', false, $message );

	if ( ! $handled ) {
		$handled = creative_agency_contact_email( $message );
	}

	creative_agency_contact_redirect( $redirect, $handled ? 'sent' : 'failed' );
}
add_action( 'template_redirect', 'creative_agency_handle_contact' );

/**
 * Redirect back to the form with a result, and stop.
 *
 * @param string $url    Where to go.
 * @param string $result Result key.
 */
function creative_agency_contact_redirect( $url, $result ) {
	wp_safe_redirect( add_query_arg( 'creative-agency-contact', $result, $url ) . '#creative-agency-contact', 303 );
	exit;
}

/**
 * Email the message to the site's admin address.
 *
 * From: is the site's own address, never the visitor's. Putting the visitor
 * there fails SPF and DMARC — the mail is sent by this server, not by their
 * provider — and it is the classic route to header injection. Reply-To carries
 * them instead, and wp_mail() rejects a header containing a newline.
 *
 * @param array $message Sanitised message.
 * @return bool
 */
function creative_agency_contact_email( $message ) {
	$to = apply_filters( 'creative_agency_contact_email_to', get_option( 'admin_email' ) );

	if ( ! $to || ! is_email( $to ) ) {
		return false;
	}

	/* translators: %s: site name. */
	$subject = sprintf( __( '[%s] New project message', 'creative-agency' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	if ( ! empty( $message['subject'] ) ) {
		$subject .= ': ' . $message['subject'];
	}
	$subject = apply_filters( 'creative_agency_contact_email_subject', $subject, $message );

	$lines  = array();
	$fields = creative_agency_contact_fields();
	foreach ( $message as $name => $value ) {
		if ( '' === $value ) {
			continue;
		}
		$label   = isset( $fields[ $name ]['label'] ) ? $fields[ $name ]['label'] : $name;
		$lines[] = $label . ': ' . $value;
	}

	$body = implode( "\n", $lines );
	$body = apply_filters( 'creative_agency_contact_email_body', $body, $message );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $message['email'] ) && is_email( $message['email'] ) ) {
		$headers[] = 'Reply-To: ' . $message['email'];
	}

	return (bool) wp_mail( $to, $subject, $body, $headers );
}
