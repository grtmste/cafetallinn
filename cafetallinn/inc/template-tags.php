<?php
/**
 * Template helpers: logo, icons, ornaments, contact and opening hours.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attachment id of an image imported on activation.
 *
 * @param string $file Seed file name, e.g. ANF_6902.jpg.
 * @return int
 */
function cafetallinn_seed_image_id( $file ) {
	$media = get_option( 'cafetallinn_seed_media', array() );
	$id    = isset( $media[ $file ] ) ? (int) $media[ $file ] : 0;
	return ( $id && get_post( $id ) ) ? $id : 0;
}

/**
 * Image id from a Customizer setting, falling back to a seeded image.
 *
 * @param string $key           Setting key without prefix.
 * @param string $fallback_file Seed file name.
 * @return int
 */
function cafetallinn_image_id( $key, $fallback_file ) {
	$id = absint( cafetallinn_info( $key ) );
	if ( $id && wp_attachment_is_image( $id ) ) {
		return $id;
	}
	return cafetallinn_seed_image_id( $fallback_file );
}

/**
 * Id of a page created or adopted on activation.
 *
 * @param string $key Page key (avaleht, menuu, joogikaart, broneeri, galerii, kontakt, catering, perekaart, kkk).
 * @return int
 */
function cafetallinn_page_id( $key ) {
	$pages = get_option( 'cafetallinn_pages', array() );
	$id    = isset( $pages[ $key ] ) ? (int) $pages[ $key ] : 0;
	if ( $id && 'publish' === get_post_status( $id ) ) {
		return $id;
	}
	$page = get_page_by_path( $key );
	return $page ? (int) $page->ID : 0;
}

/**
 * URL of a seeded page, or a fallback.
 *
 * @param string $key      Page key.
 * @param string $fallback Fallback URL.
 * @return string
 */
function cafetallinn_page_url( $key, $fallback = '' ) {
	$id = cafetallinn_page_id( $key );
	return $id ? get_permalink( $id ) : ( $fallback ? $fallback : home_url( '/' ) );
}

/**
 * The site logo. Uses the Customizer logo, then the bundled logo file.
 *
 * @param string $context header|footer|menu.
 */
function cafetallinn_logo( $context = 'header' ) {
	$name    = get_bloginfo( 'name' );
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$sizes   = array(
		'header' => '(min-width: 1024px) 150px, 110px',
		'footer' => '180px',
		'menu'   => '96px',
	);
	$label   = __( 'Café Tallinn – avalehele', 'cafetallinn' );

	echo '<a class="logo logo--' . esc_attr( $context ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( $label ) . '">';
	if ( $logo_id && wp_attachment_is_image( $logo_id ) ) {
		echo wp_get_attachment_image(
			$logo_id,
			'medium_large',
			false,
			array(
				'class'         => 'logo__img',
				'alt'           => 'Café Tallinn',
				'sizes'         => $sizes[ $context ],
				'loading'       => 'header' === $context ? 'eager' : 'lazy',
				'fetchpriority' => 'header' === $context ? 'high' : 'auto',
				'decoding'      => 'async',
			)
		);
	} else {
		printf(
			'<img class="logo__img" src="%s" width="970" height="772" alt="%s">',
			esc_url( CAFETALLINN_URI . '/assets/images/seed/cafe-tallinn-logo.png' ),
			esc_attr( $name ? $name : 'Café Tallinn' )
		);
	}
	echo '</a>';
}

/**
 * Inline SVG icons (drawn for this theme).
 *
 * @param string $name Icon name.
 * @return string
 */
function cafetallinn_icon( $name ) {
	$paths = array(
		'phone'     => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2Z"/>',
		'mail'      => '<path d="M3 5h18v14H3z"/><path d="m3 6 9 7 9-7"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><path d="M12 12.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>',
		'clock'     => '<path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/><path d="M12 7v5l3 2"/>',
		'facebook'  => '<path d="M14 21v-7.5h2.6l.4-3H14V8.6c0-.9.3-1.5 1.6-1.5H17V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.7 1.4-3.7 3.9v2.3H8.5v3H11V21"/>',
		'instagram' => '<path d="M3.5 3.5h17v17h-17z"/><path d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M17 6.8v.1"/>',
		'arrow'     => '<path d="M4 12h15"/><path d="m14 7 5 5-5 5"/>',
		'close'     => '<path d="m5 5 14 14M19 5 5 19"/>',
		'prev'      => '<path d="m15 5-7 7 7 7"/>',
		'next'      => '<path d="m9 5 7 7-7 7"/>',
		'external'  => '<path d="M14 4h6v6"/><path d="M20 4 11 13"/><path d="M18 14v6H4V6h6"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="square" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Thin gold divider with a small centred diamond.
 *
 * @param string $class Extra class.
 */
function cafetallinn_ornament( $class = '' ) {
	echo '<div class="ornament ' . esc_attr( $class ) . '" aria-hidden="true"><span class="ornament__line"></span>'
		. '<svg class="ornament__mark" viewBox="0 0 28 12" width="28" height="12"><path d="M14 1 19 6l-5 5-5-5Z" fill="none" stroke="currentColor"/><path d="M14 4.2 15.8 6 14 7.8 12.2 6Z" fill="currentColor"/><path d="M0 6h7M21 6h7" stroke="currentColor"/></svg>'
		. '<span class="ornament__line"></span></div>';
}

/**
 * tel: link target for a phone number.
 *
 * @param string $phone Phone number.
 * @return string
 */
function cafetallinn_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Opening hour lines split into [days, time].
 *
 * @param string $text One line per row.
 * @return array
 */
function cafetallinn_hours_rows( $text ) {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		if ( preg_match( '/^(\S+)\s+(.+)$/u', $line, $m ) ) {
			$rows[] = array( $m[1], $m[2] );
		} else {
			$rows[] = array( $line, '' );
		}
	}
	return $rows;
}

/**
 * Opening hours as definition lists.
 *
 * @param string $heading_tag Tag for the group headings.
 */
function cafetallinn_hours( $heading_tag = 'h3' ) {
	$tag    = tag_escape( $heading_tag );
	$groups = array(
		array( cafetallinn_info( 'hours_cafe_label' ), cafetallinn_info( 'hours_cafe' ) ),
		array( cafetallinn_info( 'hours_terrace_label' ), cafetallinn_info( 'hours_terrace' ) ),
	);
	echo '<div class="hours">';
	foreach ( $groups as $group ) {
		$rows = cafetallinn_hours_rows( $group[1] );
		if ( ! $rows ) {
			continue;
		}
		echo '<div class="hours__group">';
		if ( $group[0] ) {
			echo '<' . $tag . ' class="hours__title">' . esc_html( $group[0] ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag_escape().
		}
		echo '<dl class="hours__list">';
		foreach ( $rows as $row ) {
			echo '<div class="hours__row"><dt>' . esc_html( $row[0] ) . '</dt><dd>' . esc_html( $row[1] ) . '</dd></div>';
		}
		echo '</dl></div>';
	}
	echo '</div>';
}

/**
 * Social links.
 *
 * @param string $class Extra class.
 */
function cafetallinn_social( $class = '' ) {
	$links = array(
		'facebook'  => array( cafetallinn_info( 'facebook' ), 'Facebook' ),
		'instagram' => array( cafetallinn_info( 'instagram' ), 'Instagram' ),
	);
	$links = array_filter(
		$links,
		function ( $l ) {
			return ! empty( $l[0] );
		}
	);
	if ( ! $links ) {
		return;
	}
	echo '<ul class="social ' . esc_attr( $class ) . '">';
	foreach ( $links as $icon => $l ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener">%2$s<span class="screen-reader-text">%3$s</span></a></li>',
			esc_url( $l[0] ),
			cafetallinn_icon( $icon ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
			/* translators: %s: social network name. */
			esc_html( sprintf( __( '%s (avaneb uues aknas)', 'cafetallinn' ), $l[1] ) )
		);
	}
	echo '</ul>';
}

/**
 * Address, phone and e-mail as a list.
 *
 * @param bool $with_address Include the address row.
 */
function cafetallinn_contact_list( $with_address = true ) {
	$address = cafetallinn_info( 'address' );
	$phone   = cafetallinn_info( 'phone' );
	$email   = cafetallinn_info( 'email' );
	echo '<ul class="contact-list">';
	if ( $with_address && $address ) {
		$link = cafetallinn_info( 'map_link' );
		echo '<li>' . cafetallinn_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		if ( $link ) {
			echo '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener">' . esc_html( $address ) . '</a>';
		} else {
			echo '<span>' . esc_html( $address ) . '</span>';
		}
		echo '</li>';
	}
	if ( $phone ) {
		echo '<li>' . cafetallinn_icon( 'phone' ) . '<a href="' . esc_url( cafetallinn_tel_href( $phone ), array( 'tel' ) ) . '">' . esc_html( $phone ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
	if ( $email ) {
		echo '<li>' . cafetallinn_icon( 'mail' ) . '<a href="' . esc_url( 'mailto:' . antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
	echo '</ul>';
}

/**
 * Page heading: the "Lehe pealkiri" field, or the page title.
 *
 * @param int|null $post_id Post id.
 * @return string
 */
function cafetallinn_page_heading( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$heading = get_post_meta( $post_id, '_cafetallinn_heading', true );
	return $heading ? $heading : get_the_title( $post_id );
}

/**
 * Standard page header: eyebrow (page title), H1, ornament.
 *
 * @param string $class Extra class.
 */
function cafetallinn_page_header( $class = '' ) {
	$title   = get_the_title();
	$heading = cafetallinn_page_heading();
	echo '<header class="page-header ' . esc_attr( $class ) . '">';
	if ( $heading !== $title ) {
		echo '<p class="eyebrow">' . esc_html( $title ) . '</p>';
	}
	echo '<h1 class="page-header__title">' . esc_html( $heading ) . '</h1>';
	cafetallinn_ornament( 'page-header__ornament' );
	echo '</header>';
}

/**
 * Is the "Broneeri" form missing? (Shown to editors only.)
 */
function cafetallinn_form_hint() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	echo '<div class="editor-hint"><p>'
		. esc_html__( 'Siia tuleb broneerimisvorm. Lisa lehe sisusse Contact Form 7 või WPForms lühikood, nt [contact-form-7 id="…"].', 'cafetallinn' )
		. '</p><p><a href="' . esc_url( get_edit_post_link() ) . '">' . esc_html__( 'Muuda lehte', 'cafetallinn' ) . '</a></p></div>';
}
