<?php
/**
 * Meta description and Restaurant / CafeOrCoffeeShop JSON-LD.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin handling meta tags?
 *
 * @return bool
 */
function cafetallinn_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'The_SEO_Framework\Load' );
}

/**
 * Meta description from the page field.
 */
function cafetallinn_meta_description() {
	if ( cafetallinn_has_seo_plugin() || ! is_singular() ) {
		return;
	}
	$desc = get_post_meta( get_queried_object_id(), '_cafetallinn_meta_description', true );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'cafetallinn_meta_description', 2 );

/**
 * Convert "E-L 10.00 - 20.00" rows to schema.org OpeningHoursSpecification.
 *
 * @param string $text Hours text.
 * @return array
 */
function cafetallinn_hours_schema( $text ) {
	$days = array(
		'E' => 'Monday',
		'T' => 'Tuesday',
		'K' => 'Wednesday',
		'N' => 'Thursday',
		'R' => 'Friday',
		'L' => 'Saturday',
		'P' => 'Sunday',
	);
	$keys = array_keys( $days );
	$out  = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		if ( ! preg_match( '/^\s*([ETKNRLP])\s*(?:-\s*([ETKNRLP]))?\s+(\d{1,2})[.:](\d{2})\s*-\s*(\d{1,2})[.:](\d{2})/u', $line, $m ) ) {
			continue;
		}
		$from = array_search( $m[1], $keys, true );
		$to   = $m[2] ? array_search( $m[2], $keys, true ) : $from;
		if ( false === $from || false === $to || $to < $from ) {
			continue;
		}
		$list = array();
		for ( $i = $from; $i <= $to; $i++ ) {
			$list[] = $days[ $keys[ $i ] ];
		}
		$out[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => $list,
			'opens'     => sprintf( '%02d:%s', $m[3], $m[4] ),
			'closes'    => sprintf( '%02d:%s', $m[5], $m[6] ),
		);
	}
	return $out;
}

/**
 * JSON-LD on the front page.
 */
function cafetallinn_json_ld() {
	if ( ! is_front_page() ) {
		return;
	}
	$logo_id  = (int) get_theme_mod( 'custom_logo' );
	$hero_id  = (int) get_post_thumbnail_id( get_queried_object_id() );
	$same_as  = array_values( array_filter( array( cafetallinn_info( 'facebook' ), cafetallinn_info( 'instagram' ) ) ) );
	$address  = cafetallinn_info( 'address' );
	$phone    = cafetallinn_info( 'phone' );
	$email    = cafetallinn_info( 'email' );
	$data     = array(
		'@context'           => 'https://schema.org',
		'@type'              => array( 'CafeOrCoffeeShop', 'Restaurant' ),
		'@id'                => home_url( '/#cafe' ),
		'name'               => 'Café Tallinn',
		'url'                => home_url( '/' ),
		'acceptsReservations' => true,
		'hasMenu'            => cafetallinn_page_url( 'menuu', home_url( '/#menyy' ) ),
		'address'            => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $address,
			'addressLocality' => 'Haapsalu',
			'addressCountry'  => 'EE',
		),
	);
	if ( $phone ) {
		$data['telephone'] = preg_replace( '/[^0-9+]/', '', $phone );
	}
	if ( $email ) {
		$data['email'] = $email;
	}
	if ( $logo_id ) {
		$data['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	if ( $hero_id ) {
		$data['image'] = wp_get_attachment_image_url( $hero_id, 'large' );
	}
	$hours = cafetallinn_hours_schema( cafetallinn_info( 'hours_cafe' ) );
	if ( $hours ) {
		$data['openingHoursSpecification'] = $hours;
	}
	if ( $same_as ) {
		$data['sameAs'] = $same_as;
	}
	$map = cafetallinn_info( 'map_link' );
	if ( $map ) {
		$data['hasMap'] = $map;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n";
}
add_action( 'wp_head', 'cafetallinn_json_ld', 20 );
