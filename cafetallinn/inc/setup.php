<?php
/**
 * Theme supports, menus and small front-end filters.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and menu locations.
 */
function cafetallinn_setup() {
	load_theme_textdomain( 'cafetallinn', CAFETALLINN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 772,
			'width'                => 970,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => false,
		)
	);
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Peamenüü', 'cafetallinn' ),
			'footer'  => __( 'Jaluse menüü', 'cafetallinn' ),
		)
	);

	add_image_size( 'cafetallinn-tile', 720, 900, true );
}
add_action( 'after_setup_theme', 'cafetallinn_setup' );

/**
 * Content width for embeds.
 */
function cafetallinn_content_width() {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'cafetallinn_content_width', 0 );

/**
 * Generate WebP sub-sizes for JPEG uploads when the server supports it.
 *
 * @param array $formats Mime type map.
 * @return array
 */
function cafetallinn_webp_output( $formats ) {
	if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		$formats['image/jpeg'] = 'image/webp';
	}
	return $formats;
}
add_filter( 'image_editor_output_format', 'cafetallinn_webp_output' );

// Load only the CSS of blocks that are actually on the page.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

// No emoji polyfill on the front end.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Class names of nav items that open a modal, mapped to the modal id.
 *
 * @return array
 */
function cafetallinn_modal_classes() {
	return array(
		'js-open-menu'   => 'menyy',
		'js-open-drinks' => 'joogikaart',
	);
}

/**
 * Nav items that carry js-open-menu / js-open-drinks become dialog triggers.
 * The href stays a real URL so the link still works without JavaScript.
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @return array
 */
function cafetallinn_nav_link_attributes( $atts, $item ) {
	foreach ( cafetallinn_modal_classes() as $class => $modal ) {
		if ( in_array( $class, (array) $item->classes, true ) ) {
			$atts['data-modal']    = $modal;
			$atts['aria-haspopup'] = 'dialog';
			$atts['aria-controls'] = $modal;
		}
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'cafetallinn_nav_link_attributes', 10, 2 );

/**
 * A modal link is not "the current page".
 *
 * @param array   $classes Classes.
 * @param WP_Post $item    Menu item.
 * @return array
 */
function cafetallinn_nav_css_class( $classes, $item ) {
	if ( array_intersect( array_keys( cafetallinn_modal_classes() ), (array) $item->classes ) ) {
		$classes = array_diff( $classes, array( 'current-menu-item', 'current_page_item' ) );
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'cafetallinn_nav_css_class', 10, 2 );

/**
 * Mark the document as JS-capable as early as possible (used to hide the
 * no-JS fallbacks and to enable the modals).
 */
function cafetallinn_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'cafetallinn_js_class', 0 );

/**
 * Pingback header for singular views.
 */
function cafetallinn_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'cafetallinn_pingback_header' );

/**
 * The content is Estonian whatever the admin language: <html lang="et">.
 *
 * @param string $output Attributes.
 * @return string
 */
function cafetallinn_language_attributes( $output ) {
	return preg_replace( '/lang="[^"]*"/', 'lang="et"', $output );
}
add_filter( 'language_attributes', 'cafetallinn_language_attributes' );

// The primary menu is printed twice (desktop + mobile): drop the li ids.
add_filter( 'nav_menu_item_id', '__return_empty_string' );

/**
 * Fallback navigation before a menu is assigned.
 *
 * @param array $args wp_nav_menu() args.
 */
function cafetallinn_menu_fallback( $args ) {
	$items = array(
		'avaleht'    => array( __( 'Avaleht', 'cafetallinn' ), '' ),
		'menuu'      => array( __( 'Menüü', 'cafetallinn' ), 'menyy' ),
		'joogikaart' => array( __( 'Joogikaart', 'cafetallinn' ), 'joogikaart' ),
		'broneeri'   => array( __( 'Broneeri', 'cafetallinn' ), '' ),
		'galerii'    => array( __( 'Galerii', 'cafetallinn' ), '' ),
		'kontakt'    => array( __( 'Kontakt', 'cafetallinn' ), '' ),
	);
	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '">';
	foreach ( $items as $key => $item ) {
		$url = 'avaleht' === $key ? home_url( '/' ) : cafetallinn_page_url( $key, home_url( '/' . ( $item[1] ? '#' . $item[1] : $key . '/' ) ) );
		$att = $item[1] ? ' data-modal="' . esc_attr( $item[1] ) . '" aria-haspopup="dialog" aria-controls="' . esc_attr( $item[1] ) . '"' : '';
		echo '<li class="menu-item"><a href="' . esc_url( $url ) . '"' . $att . '>' . esc_html( $item[0] ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
	}
	echo '</ul>';
}
