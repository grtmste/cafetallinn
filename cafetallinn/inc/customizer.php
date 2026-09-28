<?php
/**
 * Customizer panel "Kohviku info".
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values (taken from the existing site).
 *
 * @return array
 */
function cafetallinn_defaults() {
	return array(
		'address'             => 'Rannarootsi keskus, Haapsalu',
		'phone'               => '+372 56630243',
		'email'               => 'info@cafetallinn.ee',
		'hours_cafe_label'    => 'Kohvik avatud',
		'hours_cafe'          => "E-L 10.00 - 20.00\nP 10.00 - 19.00",
		'hours_terrace_label' => 'Terrass avatud',
		'hours_terrace'       => "E-N 10.00-20.00\nR-L 10.00-23.00\nP 10.00-19.00",
		'facebook'            => 'https://www.facebook.com/cafetallinn',
		'instagram'           => '',
		'map_embed'           => 'https://maps.google.com/maps?q=Cafe%20Tallinn%2C%20Rannarootsi%20keskus%2C%20Haapsalu&z=16&output=embed',
		'map_link'            => 'https://www.google.com/maps/search/?api=1&query=Cafe+Tallinn+Rannarootsi+Haapsalu',
		'map_autoload'        => true,
		'tile_menu_show'      => true,
		'tile_drinks_show'    => true,
		'tile_events_show'    => true,
		'tile_catering_show'  => true,
		'hero_image_2'        => 0,
		'tile_menu_image'     => 0,
		'tile_drinks_image'   => 0,
		'tile_events_image'   => 0,
		'tile_catering_image' => 0,
	);
}

/**
 * Read a "Kohviku info" value.
 *
 * @param string $key Setting key without prefix.
 * @return mixed
 */
function cafetallinn_info( $key ) {
	$defaults = cafetallinn_defaults();
	return get_theme_mod( 'cafetallinn_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Sanitize a phone number (digits, spaces, +, -, parentheses).
 *
 * @param string $value Raw value.
 * @return string
 */
function cafetallinn_sanitize_phone( $value ) {
	return trim( preg_replace( '/[^0-9+\-\s()]/', '', (string) $value ) );
}

/**
 * Only allow https Google Maps / OpenStreetMap embed URLs.
 *
 * @param string $value Raw value.
 * @return string
 */
function cafetallinn_sanitize_map_embed( $value ) {
	$value = esc_url_raw( trim( (string) $value ), array( 'https' ) );
	if ( '' === $value ) {
		return '';
	}
	$host    = wp_parse_url( $value, PHP_URL_HOST );
	$allowed = '/(^|\.)(google\.[a-z.]+|openstreetmap\.org)$/i';
	return ( $host && preg_match( $allowed, $host ) ) ? $value : '';
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function cafetallinn_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Register the panel.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function cafetallinn_customize_register( $wp_customize ) {
	$d = cafetallinn_defaults();

	$wp_customize->add_panel(
		'cafetallinn_info',
		array(
			'title'    => __( 'Kohviku info', 'cafetallinn' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'cafetallinn_contact' => __( 'Kontaktandmed', 'cafetallinn' ),
		'cafetallinn_hours'   => __( 'Lahtiolekuajad', 'cafetallinn' ),
		'cafetallinn_social'  => __( 'Sotsiaalmeedia', 'cafetallinn' ),
		'cafetallinn_map'     => __( 'Kaart', 'cafetallinn' ),
		'cafetallinn_home'    => __( 'Avaleht: pildid ja plokid', 'cafetallinn' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'cafetallinn_info' ) );
	}

	$fields = array(
		// key, section, label, type, sanitize, description.
		array( 'address', 'cafetallinn_contact', __( 'Aadress', 'cafetallinn' ), 'text', 'sanitize_text_field', '' ),
		array( 'phone', 'cafetallinn_contact', __( 'Telefon', 'cafetallinn' ), 'text', 'cafetallinn_sanitize_phone', '' ),
		array( 'email', 'cafetallinn_contact', __( 'E-post', 'cafetallinn' ), 'email', 'sanitize_email', '' ),
		array( 'hours_cafe_label', 'cafetallinn_hours', __( 'Pealkiri 1', 'cafetallinn' ), 'text', 'sanitize_text_field', '' ),
		array( 'hours_cafe', 'cafetallinn_hours', __( 'Lahtiolekuajad 1', 'cafetallinn' ), 'textarea', 'sanitize_textarea_field', __( 'Üks rida päeva kohta, nt "E-L 10.00 - 20.00". Päevad: E T K N R L P.', 'cafetallinn' ) ),
		array( 'hours_terrace_label', 'cafetallinn_hours', __( 'Pealkiri 2', 'cafetallinn' ), 'text', 'sanitize_text_field', '' ),
		array( 'hours_terrace', 'cafetallinn_hours', __( 'Lahtiolekuajad 2', 'cafetallinn' ), 'textarea', 'sanitize_textarea_field', __( 'Jäta tühjaks, kui teist plokki pole vaja.', 'cafetallinn' ) ),
		array( 'facebook', 'cafetallinn_social', __( 'Facebooki link', 'cafetallinn' ), 'url', 'esc_url_raw', '' ),
		array( 'instagram', 'cafetallinn_social', __( 'Instagrami link', 'cafetallinn' ), 'url', 'esc_url_raw', '' ),
		array( 'map_embed', 'cafetallinn_map', __( 'Kaardi manustamise URL', 'cafetallinn' ), 'url', 'cafetallinn_sanitize_map_embed', __( 'Google Maps → Jaga → Manusta kaart → kopeeri src="…" väärtus.', 'cafetallinn' ) ),
		array( 'map_link', 'cafetallinn_map', __( 'Link "Ava kaardil"', 'cafetallinn' ), 'url', 'esc_url_raw', '' ),
		array( 'map_autoload', 'cafetallinn_map', __( 'Laadi kaart kohe (ilma klõpsuta)', 'cafetallinn' ), 'checkbox', 'cafetallinn_sanitize_checkbox', __( 'Välja lülitades näidatakse enne nuppu "Näita kaarti" ja Google Maps laaditakse alles pärast klõpsu (privaatsussõbralikum, kiirem).', 'cafetallinn' ) ),
	);

	foreach ( $fields as $f ) {
		list( $key, $section, $label, $type, $sanitize, $desc ) = $f;
		$wp_customize->add_setting(
			'cafetallinn_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'cafetallinn_' . $key,
			array(
				'label'       => $label,
				'description' => $desc,
				'section'     => $section,
				'type'        => $type,
			)
		);
	}

	$images = array(
		'hero_image_2'        => __( 'Avalehe teine pilt (esimene on lehe "Avaleht" tunuspilt)', 'cafetallinn' ),
		'tile_menu_image'     => __( 'Plokk "Menüü"', 'cafetallinn' ),
		'tile_drinks_image'   => __( 'Plokk "Joogikaart"', 'cafetallinn' ),
		'tile_events_image'   => __( 'Plokk "Sündmused"', 'cafetallinn' ),
		'tile_catering_image' => __( 'Plokk "Catering"', 'cafetallinn' ),
	);
	foreach ( $images as $key => $label ) {
		$wp_customize->add_setting(
			'cafetallinn_' . $key,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'cafetallinn_' . $key,
				array(
					'label'     => $label,
					'section'   => 'cafetallinn_home',
					'mime_type' => 'image',
				)
			)
		);
	}
}
add_action( 'customize_register', 'cafetallinn_customize_register' );

/**
 * Show/hide switches for the four front-page tiles.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function cafetallinn_customize_tiles( $wp_customize ) {
	$tiles = array(
		'tile_menu_show'     => __( 'Näita plokki "Menüü"', 'cafetallinn' ),
		'tile_drinks_show'   => __( 'Näita plokki "Joogikaart"', 'cafetallinn' ),
		'tile_events_show'   => __( 'Näita plokki "Sündmused"', 'cafetallinn' ),
		'tile_catering_show' => __( 'Näita plokki "Catering"', 'cafetallinn' ),
	);
	$priority = 1;
	foreach ( $tiles as $key => $label ) {
		$wp_customize->add_setting(
			'cafetallinn_' . $key,
			array(
				'default'           => true,
				'sanitize_callback' => 'cafetallinn_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'cafetallinn_' . $key,
			array(
				'label'    => $label,
				'section'  => 'cafetallinn_home',
				'type'     => 'checkbox',
				'priority' => $priority++,
			)
		);
	}
}
add_action( 'customize_register', 'cafetallinn_customize_tiles', 20 );

