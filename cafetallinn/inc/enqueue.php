<?php
/**
 * Styles, scripts and font preloading.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset URL: minified by default, source files when SCRIPT_DEBUG is on.
 *
 * @param string $type css|js.
 * @return string
 */
function cafetallinn_asset( $type ) {
	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$file  = $debug ? "assets/{$type}/src/main.{$type}" : "assets/{$type}/main.min.{$type}";
	return CAFETALLINN_URI . '/' . $file;
}

/**
 * Enqueue front-end assets.
 */
function cafetallinn_enqueue() {
	wp_enqueue_style( 'cafetallinn', cafetallinn_asset( 'css' ), array(), CAFETALLINN_VERSION );

	wp_enqueue_script(
		'cafetallinn',
		cafetallinn_asset( 'js' ),
		array(),
		CAFETALLINN_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script(
		'cafetallinn',
		'cafetallinnL10n',
		array(
			'close'    => __( 'Sulge', 'cafetallinn' ),
			'prev'     => __( 'Eelmine pilt', 'cafetallinn' ),
			'next'     => __( 'Järgmine pilt', 'cafetallinn' ),
			/* translators: 1: current image number, 2: total number of images. */
			'counter'  => __( '%1$s / %2$s', 'cafetallinn' ),
			'lightbox' => __( 'Pildivaade', 'cafetallinn' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'cafetallinn_enqueue' );

/**
 * Preload the two fonts used above the fold.
 */
function cafetallinn_preload_fonts() {
	$fonts = array(
		'jost-latin-wght-normal.woff2',
		'cormorant-garamond-latin-500-normal.woff2',
	);
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( CAFETALLINN_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'cafetallinn_preload_fonts', 1 );
