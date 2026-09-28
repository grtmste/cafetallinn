<?php
/**
 * One-time setup on theme activation.
 *
 * Creates (or adopts) the pages, sets the static front page, seeds the menu
 * and drink items, imports the images, sets logo / site icon and builds the
 * navigation menus. Every step is idempotent and remembered in the
 * `cafetallinn_setup` option, so re-activating the theme duplicates nothing
 * and does not bring back items the client has deleted. Image import runs in
 * time-boxed batches and resumes on the next admin page load if needed.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

const CAFETALLINN_SETUP_OPTION = 'cafetallinn_setup';

/**
 * Seed images: file => [alt text, title].
 *
 * @return array
 */
function cafetallinn_seed_images() {
	return array(
		'cafe-tallinn-logo.png'  => array( 'Café Tallinn', 'Café Tallinn logo' ),
		'cafe-tallinn-ikoon.png' => array( 'Café Tallinn', 'Café Tallinn ikoon' ),
		'ANF_6945.jpg'           => array( 'Café Tallinna saal messingvalgustite, kriiditahvli-menüü ja rohelise diivaniga', 'ANF_6945' ),
		'ANF_6902.jpg'           => array( 'Café Tallinna sissepääs Rannarootsi keskuses', 'ANF_6902' ),
		'ANF_6909.jpg'           => array( 'Kohviku saal lillade samettoolide, leti ja kriiditahvli-menüüga', 'ANF_6909' ),
		'ANF_6914.jpg'           => array( 'Valgusküllane saal rotangvalgustite ja suurte akendega', 'ANF_6914' ),
		'ANF_6916.jpg'           => array( 'Riiulinurk akende ääres, taamal kohvimasin', 'ANF_6916' ),
		'ANF_6925.jpg'           => array( 'Kaks rohelist tugitooli ja laud akna all', 'ANF_6925' ),
		'ANF_6927.jpg'           => array( 'Lastenurk söögitooli, raamatute ja mänguasjadega', 'ANF_6927' ),
		'ANF_6854.jpg'           => array( 'Koorene supp ürtidega', 'ANF_6854' ),
		'ANF_6817.jpg'           => array( 'Salat küpsetatud juustu, maasikate, mustikate ja füüsalitega', 'ANF_6817' ),
		'ANF_6790.jpg'           => array( 'Šokolaadikook jäätise, karamelli ja maasikatega', 'ANF_6790' ),
		'ANF_6726.jpg'           => array( 'Burger friikartulitega', 'ANF_6726' ),
		'ANF_6687.jpg'           => array( 'Täidetud pannkook salati ja dipikastmega', 'ANF_6687' ),
		'ANF_6649.jpg'           => array( 'Sinimerekarbid sidruni ja leivaga', 'ANF_6649' ),
		'ANF_6611.jpg'           => array( 'Paneeritud šnitsel friikartulite, kirsstomatite ja rukolaga', 'ANF_6611' ),
		'ANF_6615.jpg'           => array( 'Lõhesteik salatipadjal kartulite ja suvikõrvitsaga', 'ANF_6615' ),
	);
}

/**
 * Gallery order (as on the current Galerii page).
 *
 * @return string[]
 */
function cafetallinn_gallery_files() {
	return array( 'ANF_6925.jpg', 'ANF_6916.jpg', 'ANF_6914.jpg', 'ANF_6909.jpg', 'ANF_6945.jpg', 'ANF_6927.jpg', 'ANF_6902.jpg', 'ANF_6854.jpg', 'ANF_6817.jpg', 'ANF_6790.jpg', 'ANF_6726.jpg', 'ANF_6687.jpg', 'ANF_6649.jpg', 'ANF_6611.jpg', 'ANF_6615.jpg' );
}

/**
 * Shortcode block for an existing Contact Form 7 form, if it exists here.
 *
 * @param string $hash  CF7 form hash from the old site.
 * @param string $title Form title.
 * @return string
 */
function cafetallinn_cf7_block( $hash, $title ) {
	if ( function_exists( 'wpcf7_get_contact_form_by_hash' ) && wpcf7_get_contact_form_by_hash( $hash ) ) {
		return "<!-- wp:shortcode -->\n[contact-form-7 id=\"{$hash}\" title=\"{$title}\"]\n<!-- /wp:shortcode -->";
	}
	return '';
}

/**
 * Page definitions with the existing site's content (verbatim).
 *
 * @return array
 */
function cafetallinn_seed_pages() {
	$p = function ( $html ) {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
	};

	$story = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Meie lugu</h2>\n<!-- /wp:heading -->\n\n"
		. $p( 'Café Tallinn kohvik asub Haapsalu Rannarootsi keskuses, olles justkui peidetud pärl, mis ootab avastamist. <br>Meie kohviku atmosfäär on täis hubasust, pehmeid toone ja mugavaid istekohti, mis kutsub sind hetkeks argipäeva unustama. <br>Café Tallinn kohvik on rohkem kui lihtsalt koht kohvi joomiseks – see on koht, kus saad jagada hetki sõpradega või leida rahu iseendale. <br>Meie sõbralik ja külalislahke personal on alati valmis sind tervitama naeratusega ning tagama, et sinu külastus jääks meelde kui eriline kogemus.<br>Oleme avatud selleks, et muuta iga päev eriliseks.<br>Tule ja avasta Café Tallinn kohvik – sinu kohv, sinu kohvik!' );

	$perekaart = "<!-- wp:paragraph {\"className\":\"is-style-lead\"} -->\n<p class=\"is-style-lead\">Meie kohvikus saab perekaardiga* -10% soodustust kogu menüült.</p>\n<!-- /wp:paragraph -->\n\n"
		. $p( '*Perekaart on sooduskaart, mis on loodud Eesti lasterikastele peredele, kus on kasvamas kolm või enam last. Perekaardi kasutajana on võimalik luua lisaväärtust oma perele - külastada soodsamalt muuseume, teatreid ja teemaparke ning teha taskukohaseid sisseoste. Perekaardi programmiga on liitunud üle 500 Eesti ettevõtte' )
		. "\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"https://perekaart.ee/\">Rohkem infot perekaardi kohta</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->";

	$faq = array(
		array( 'Mis kell suletakse köök?', 'Köök läheb kinni 15 minutit enne kohviku sulgemist.' ),
		array( 'Kas ma tohin oma lemmiklooma kohvikusse kaasa võtta?', 'Jah, hästi käituvad lemmikloomad on oodatud.' ),
		array( 'Kas kohvikus pakutakse taimetoitu?', 'Jah, meie menüüs on mõned road ka taimetoitlastele.' ),
		array( 'Kuidas ma saan lauda reserveerida?', 'Reserveerimiseks saad helistada meie numbrile <a href="tel:+3724729021">+372 472 9021, </a>kirjutada meile facebooki sõnumitesse <a href="http://www.facebook.com/cafetallinn">www.facebook.com/cafetallinn</a> või broneerida läbi kodulehe.' ),
		array( 'Kas kohvi on võimalik osta taimse piimaga?', 'Jah, meilt saab kohvi nii kaerapiima kui ka mandlipiimaga.' ),
		array( 'Kas toitu on võimalik kaasa osta?', 'Jah, meie juurest on võimalik toitu kaasa osta ning oma karbiga tulles, saab soodustust -10%.' ),
		array( 'Kas kohvikus kehtib ka Perekaart?', 'Meie kohvikus saab perekaardiga* -10% soodustust kogu menüült.<br>*Perekaart on sooduskaart, mis on loodud Eesti lasterikastele peredele, kus on kasvamas kolm või enam last.' ),
	);
	$kkk = '';
	foreach ( $faq as $qa ) {
		$kkk .= "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>{$qa[0]}</summary>" . $p( $qa[1] ) . "</details>\n<!-- /wp:details -->\n\n";
	}

	return array(
		'avaleht'    => array(
			'title'    => 'Avaleht',
			'slugs'    => array( 'avaleht' ),
			'template' => '',
			'heading'  => '',
			'content'  => $story,
			'desc'     => 'Café Tallinn kohvik asub Haapsalu Rannarootsi keskuses, olles justkui peidetud pärl, mis ootab avastamist.',
		),
		'menuu'      => array(
			'title'    => 'Menüü',
			'slugs'    => array( 'menuu' ),
			'template' => 'page-menuu.php',
			'heading'  => '',
			'content'  => '',
			'desc'     => '',
		),
		'joogikaart' => array(
			'title'    => 'Joogikaart',
			'slugs'    => array( 'joogikaart' ),
			'template' => 'page-joogikaart.php',
			'heading'  => '',
			'content'  => null, // Keep whatever is there (not rendered by the template).
			'desc'     => '',
		),
		'broneeri'   => array(
			'title'    => 'Broneeri',
			'slugs'    => array( 'broneeri' ),
			'template' => 'page-broneeri.php',
			'heading'  => 'Broneeri oma eriline sündmus meie juures juba täna!',
			'content'  => cafetallinn_cf7_block( '05e7e64', 'Kontaktivorm 1' ),
			'desc'     => '',
		),
		'galerii'    => array(
			'title'    => 'Galerii',
			'slugs'    => array( 'galerii' ),
			'template' => 'page-galerii.php',
			'heading'  => '',
			'content'  => '', // Gallery block is added once the images are imported.
			'gallery'  => true,
			'desc'     => '',
		),
		'kontakt'    => array(
			'title'    => 'Kontakt',
			'slugs'    => array( 'kontakt', 'kontaktid' ),
			'template' => 'page-kontakt.php',
			'heading'  => 'Võta meiega ühendust!',
			'content'  => '',
			'desc'     => '',
		),
		'catering'   => array(
			'title'    => 'Catering',
			'slugs'    => array( 'catering' ),
			'template' => 'page-broneeri.php',
			'heading'  => 'Telli enda erilisteks üritusteks soojad toidud, salatid ja suupisted meie juurest!',
			'content'  => cafetallinn_cf7_block( '5d1b4d2', 'Catering' ),
			'desc'     => '',
		),
		'perekaart'  => array(
			'title'    => 'Perekaart',
			'slugs'    => array( 'perekaart' ),
			'template' => '',
			'heading'  => 'Perekaart- unistustest võimalusteni',
			'content'  => $perekaart,
			'desc'     => '',
		),
		'kkk'        => array(
			'title'    => 'KKK',
			'slugs'    => array( 'kkk' ),
			'template' => '',
			'heading'  => 'KKK- Korduma Kippuvad Küsimused',
			'content'  => trim( $kkk ),
			'desc'     => '',
		),
	);
}

/**
 * Current setup state.
 *
 * @return array
 */
function cafetallinn_setup_state() {
	$state = get_option( CAFETALLINN_SETUP_OPTION, array() );
	return is_array( $state ) ? $state : array();
}

/**
 * Mark a step done.
 *
 * @param string $step Step.
 */
function cafetallinn_setup_done( $step ) {
	$state            = cafetallinn_setup_state();
	$state[ $step ]   = time();
	$state['version'] = CAFETALLINN_VERSION;
	update_option( CAFETALLINN_SETUP_OPTION, $state, false );
}

/**
 * Is setup fully done?
 *
 * @return bool
 */
function cafetallinn_setup_complete() {
	$state = cafetallinn_setup_state();
	foreach ( array( 'pages', 'front', 'terms', 'items', 'nav', 'media', 'final' ) as $step ) {
		if ( empty( $state[ $step ] ) ) {
			return false;
		}
	}
	return true;
}

/**
 * Run all pending steps.
 *
 * @param int $budget Seconds available for image import.
 */
function cafetallinn_setup_run( $budget = 20 ) {
	if ( get_transient( 'cafetallinn_setup_lock' ) ) {
		return;
	}
	set_transient( 'cafetallinn_setup_lock', 1, 60 );
	$start = microtime( true );
	$state = cafetallinn_setup_state();

	if ( empty( $state['pages'] ) ) {
		cafetallinn_setup_pages();
		cafetallinn_setup_done( 'pages' );
	}
	if ( empty( $state['front'] ) ) {
		cafetallinn_setup_front_page();
		cafetallinn_setup_done( 'front' );
	}
	if ( empty( $state['terms'] ) ) {
		cafetallinn_setup_terms();
		cafetallinn_setup_done( 'terms' );
	}
	if ( empty( $state['items'] ) ) {
		cafetallinn_setup_items();
		cafetallinn_setup_done( 'items' );
	}
	if ( empty( $state['nav'] ) ) {
		cafetallinn_setup_nav();
		cafetallinn_setup_done( 'nav' );
	}
	if ( empty( $state['media'] ) ) {
		$left = max( 3, $budget - (int) ( microtime( true ) - $start ) );
		if ( cafetallinn_setup_media( $left ) ) {
			cafetallinn_setup_done( 'media' );
		}
	}
	$state = cafetallinn_setup_state();
	if ( ! empty( $state['media'] ) && empty( $state['final'] ) ) {
		cafetallinn_setup_finalize();
		cafetallinn_setup_done( 'final' );
		set_transient( 'cafetallinn_setup_notice', 1, HOUR_IN_SECONDS );
	}
	delete_transient( 'cafetallinn_setup_lock' );
}

/**
 * Activation hook.
 */
function cafetallinn_after_switch_theme() {
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- may be disabled on some hosts.
	}
	cafetallinn_register_menu_cpt();
	cafetallinn_setup_run( 25 );
}
add_action( 'after_switch_theme', 'cafetallinn_after_switch_theme' );

/**
 * Resume unfinished steps (image import) on later admin page loads.
 */
function cafetallinn_setup_resume() {
	if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) || cafetallinn_setup_complete() ) {
		return;
	}
	cafetallinn_setup_run( 12 );
}
add_action( 'admin_init', 'cafetallinn_setup_resume' );

/**
 * Admin notices: progress and completion.
 */
function cafetallinn_setup_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! cafetallinn_setup_complete() ) {
		$media = get_option( 'cafetallinn_seed_media', array() );
		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: 1: imported images, 2: total images. */
					__( 'Café Tallinn: pilte imporditakse meediateeki (%1$d / %2$d). Laadi see leht uuesti, kuni teade kaob.', 'cafetallinn' ),
					count( $media ),
					count( cafetallinn_seed_images() )
				)
			)
		);
		return;
	}
	if ( get_transient( 'cafetallinn_setup_notice' ) ) {
		delete_transient( 'cafetallinn_setup_notice' );
		printf(
			'<div class="notice notice-success is-dismissible"><p>%1$s <a href="%2$s">%3$s</a> · <a href="%4$s">%5$s</a></p></div>',
			esc_html__( 'Café Tallinn on valmis: lehed, menüü, joogikaart, pildid ja navigatsioon on paigas.', 'cafetallinn' ),
			esc_url( admin_url( 'customize.php?autofocus[panel]=cafetallinn_info' ) ),
			esc_html__( 'Kohviku info', 'cafetallinn' ),
			esc_url( admin_url( 'edit.php?post_type=menu_item' ) ),
			esc_html__( 'Menüü ja joogid', 'cafetallinn' )
		);
	}
}
add_action( 'admin_notices', 'cafetallinn_setup_notices' );

/* ---------------------------------------------------------------------------
 * Steps
 * ------------------------------------------------------------------------ */

/**
 * Find an existing page by slug candidates, then by exact title.
 *
 * @param array $def Page definition.
 * @return WP_Post|null
 */
function cafetallinn_find_page( $def ) {
	foreach ( $def['slugs'] as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && 'trash' !== $page->post_status ) {
			return $page;
		}
	}
	$query = new WP_Query(
		array(
			'post_type'              => 'page',
			'title'                  => $def['title'],
			'post_status'            => array( 'publish', 'draft', 'private' ),
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	return $query->posts ? $query->posts[0] : null;
}

/**
 * Was this page built with Elementor (content would render broken without it)?
 *
 * @param WP_Post $page Page.
 * @return bool
 */
function cafetallinn_is_builder_page( $page ) {
	return 'builder' === get_post_meta( $page->ID, '_elementor_edit_mode', true );
}

/**
 * Create or adopt the pages.
 */
function cafetallinn_setup_pages() {
	$ids = get_option( 'cafetallinn_pages', array() );

	foreach ( cafetallinn_seed_pages() as $key => $def ) {
		$page = cafetallinn_find_page( $def );

		if ( ! $page ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $def['title'],
					'post_name'    => $def['slugs'][0],
					'post_content' => (string) $def['content'],
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			if ( ! empty( $def['gallery'] ) ) {
				update_post_meta( $id, '_cafetallinn_needs_gallery', 1 );
			}
		} else {
			$id = $page->ID;
			// Elementor pages: keep a backup, then replace the builder output
			// with the clean content so it renders in this theme.
			if ( cafetallinn_is_builder_page( $page ) && null !== $def['content'] ) {
				if ( '' === get_post_meta( $id, '_cafetallinn_backup_content', true ) ) {
					update_post_meta( $id, '_cafetallinn_backup_content', wp_slash( $page->post_content ) );
					update_post_meta( $id, '_cafetallinn_backup_template', get_post_meta( $id, '_wp_page_template', true ) );
				}
				delete_post_meta( $id, '_elementor_edit_mode' );
				wp_update_post(
					array(
						'ID'           => $id,
						'post_content' => wp_slash( (string) $def['content'] ),
					)
				);
				if ( ! empty( $def['gallery'] ) ) {
					update_post_meta( $id, '_cafetallinn_needs_gallery', 1 );
				}
			}
			if ( 'publish' !== $page->post_status ) {
				wp_update_post(
					array(
						'ID'          => $id,
						'post_status' => 'publish',
					)
				);
			}
		}

		$current_template = get_post_meta( $id, '_wp_page_template', true );
		if ( $def['template'] && in_array( $current_template, array( '', 'default', 'elementor_header_footer', 'elementor_canvas', 'elementor_theme' ), true ) ) {
			update_post_meta( $id, '_wp_page_template', $def['template'] );
		} elseif ( ! $def['template'] && in_array( $current_template, array( 'elementor_header_footer', 'elementor_canvas', 'elementor_theme' ), true ) ) {
			update_post_meta( $id, '_wp_page_template', 'default' );
		}
		if ( $def['heading'] && '' === get_post_meta( $id, '_cafetallinn_heading', true ) ) {
			update_post_meta( $id, '_cafetallinn_heading', $def['heading'] );
		}
		if ( $def['desc'] && '' === get_post_meta( $id, '_cafetallinn_meta_description', true ) ) {
			update_post_meta( $id, '_cafetallinn_meta_description', $def['desc'] );
		}
		$ids[ $key ] = (int) $id;
	}
	update_option( 'cafetallinn_pages', $ids, false );
}

/**
 * Avaleht as the static front page.
 */
function cafetallinn_setup_front_page() {
	$front = cafetallinn_page_id( 'avaleht' );
	if ( $front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front );
	}
}

/**
 * Seed data from inc/data/*.json.
 *
 * @param string $type menuu|joogikaart.
 * @return array
 */
function cafetallinn_seed_data( $type ) {
	$file = CAFETALLINN_DIR . '/inc/data/' . ( 'menuu' === $type ? 'menu' : 'drinks' ) . '.json';
	if ( ! is_readable( $file ) ) {
		return array();
	}
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file.
	return isset( $data['categories'] ) ? $data['categories'] : array();
}

/**
 * Categories with order and type.
 */
function cafetallinn_setup_terms() {
	foreach ( array_keys( cafetallinn_menu_types() ) as $type ) {
		foreach ( cafetallinn_seed_data( $type ) as $cat ) {
			$term = term_exists( $cat['slug'], 'menu_category' );
			if ( ! $term ) {
				$term = wp_insert_term( $cat['name'], 'menu_category', array( 'slug' => $cat['slug'] ) );
			}
			if ( is_wp_error( $term ) ) {
				continue;
			}
			$term_id = (int) $term['term_id'];
			if ( '' === get_term_meta( $term_id, '_cafetallinn_order', true ) ) {
				update_term_meta( $term_id, '_cafetallinn_order', (int) $cat['order'] );
			}
			if ( '' === get_term_meta( $term_id, '_cafetallinn_type', true ) ) {
				update_term_meta( $term_id, '_cafetallinn_type', $type );
			}
		}
	}
}

/**
 * Menu and drink items.
 */
function cafetallinn_setup_items() {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-off setup lookup.
	$existing = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", '_cafetallinn_seed_key' ) );
	$existing = array_flip( $existing );

	wp_defer_term_counting( true );
	foreach ( array_keys( cafetallinn_menu_types() ) as $type ) {
		foreach ( cafetallinn_seed_data( $type ) as $cat ) {
			$term = get_term_by( 'slug', $cat['slug'], 'menu_category' );
			foreach ( $cat['items'] as $item ) {
				$key = $type . '/' . $cat['slug'] . '/' . $item['order'];
				if ( isset( $existing[ $key ] ) ) {
					continue;
				}
				$id = wp_insert_post(
					array(
						'post_type'   => 'menu_item',
						'post_status' => 'publish',
						'post_title'  => wp_slash( $item['name'] ),
						'menu_order'  => (int) $item['order'],
						'meta_input'  => array(
							'_cafetallinn_type'        => $type,
							'_cafetallinn_price'       => wp_slash( $item['price'] ),
							'_cafetallinn_description' => wp_slash( $item['description'] ),
							'_cafetallinn_tags'        => implode( ', ', $item['tags'] ),
							'_cafetallinn_seed_key'    => $key,
						),
					),
					true
				);
				if ( ! is_wp_error( $id ) && $term ) {
					wp_set_object_terms( $id, (int) $term->term_id, 'menu_category' );
				}
			}
		}
	}
	wp_defer_term_counting( false );
}

/**
 * Attachment already in the library for a file name (e.g. 2024/01/ANF_6902.jpg on the live site).
 *
 * @param string $file File name.
 * @return int
 */
function cafetallinn_find_attachment( $file ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-off setup lookup.
	$id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
			'_cafetallinn_seed_file',
			$file
		)
	);
	if ( ! $id ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one-off setup lookup.
		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT m.post_id FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE m.meta_key = '_wp_attached_file' AND p.post_type = 'attachment' AND ( m.meta_value = %s OR m.meta_value LIKE %s ) ORDER BY m.post_id ASC LIMIT 1",
				$file,
				'%/' . $wpdb->esc_like( $file )
			)
		);
	}
	return (int) $id;
}

/**
 * Import seed images (time-boxed). Returns true when all are done.
 *
 * @param int $budget Seconds.
 * @return bool
 */
function cafetallinn_setup_media( $budget ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$start = microtime( true );
	$media = get_option( 'cafetallinn_seed_media', array() );

	foreach ( cafetallinn_seed_images() as $file => $info ) {
		if ( ! empty( $media[ $file ] ) && get_post( $media[ $file ] ) ) {
			continue;
		}
		if ( microtime( true ) - $start > $budget ) {
			update_option( 'cafetallinn_seed_media', $media, false );
			return false;
		}

		$id = cafetallinn_find_attachment( $file );
		if ( ! $id ) {
			$src = CAFETALLINN_DIR . '/assets/images/seed/' . $file;
			if ( ! is_readable( $src ) ) {
				continue;
			}
			$tmp = wp_tempnam( $file );
			copy( $src, $tmp );
			$id = media_handle_sideload(
				array(
					'name'     => $file,
					'tmp_name' => $tmp,
				),
				0,
				$info[1]
			);
			if ( is_wp_error( $id ) ) {
				wp_delete_file( $tmp );
				continue;
			}
		}
		update_post_meta( $id, '_cafetallinn_seed_file', $file );
		if ( '' === get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
			update_post_meta( $id, '_wp_attachment_image_alt', $info[0] );
		}
		$media[ $file ] = (int) $id;
		update_option( 'cafetallinn_seed_media', $media, false );
	}
	return true;
}

/**
 * Gallery block markup for the Galerii page.
 *
 * @return string
 */
function cafetallinn_gallery_block() {
	$inner = '';
	foreach ( cafetallinn_gallery_files() as $file ) {
		$id = cafetallinn_seed_image_id( $file );
		if ( ! $id ) {
			continue;
		}
		$full   = wp_get_attachment_image_url( $id, 'full' );
		$large  = wp_get_attachment_image_url( $id, 'large' );
		$alt    = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$inner .= "<!-- wp:image {\"id\":{$id},\"sizeSlug\":\"large\",\"linkDestination\":\"media\"} -->\n"
			. '<figure class="wp-block-image size-large"><a href="' . esc_url( $full ) . '"><img src="' . esc_url( $large ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $id . '"/></a></figure>' . "\n"
			. "<!-- /wp:image -->\n\n";
	}
	if ( '' === $inner ) {
		return '';
	}
	return "<!-- wp:gallery {\"linkTo\":\"media\",\"sizeSlug\":\"large\",\"imageCrop\":false} -->\n"
		. "<figure class=\"wp-block-gallery has-nested-images columns-default\">{$inner}</figure>\n"
		. '<!-- /wp:gallery -->';
}

/**
 * Logo, site icon, featured image, gallery content.
 */
function cafetallinn_setup_finalize() {
	$logo = cafetallinn_seed_image_id( 'cafe-tallinn-logo.png' );
	if ( $logo && ! get_theme_mod( 'custom_logo' ) ) {
		set_theme_mod( 'custom_logo', $logo );
	}
	$icon = cafetallinn_seed_image_id( 'cafe-tallinn-ikoon.png' );
	if ( $icon && ! get_option( 'site_icon' ) ) {
		update_option( 'site_icon', $icon );
	}
	$front = cafetallinn_page_id( 'avaleht' );
	$hero  = cafetallinn_seed_image_id( 'ANF_6945.jpg' );
	if ( $front && $hero && ! has_post_thumbnail( $front ) ) {
		set_post_thumbnail( $front, $hero );
	}
	$galerii = cafetallinn_page_id( 'galerii' );
	if ( $galerii && get_post_meta( $galerii, '_cafetallinn_needs_gallery', true ) ) {
		$block = cafetallinn_gallery_block();
		if ( $block ) {
			wp_update_post(
				array(
					'ID'           => $galerii,
					'post_content' => wp_slash( $block ),
				)
			);
			delete_post_meta( $galerii, '_cafetallinn_needs_gallery' );
		}
	}
}

/**
 * Build or adopt a nav menu and assign it to a location.
 *
 * @param string $location Location.
 * @param string $name     Menu name.
 * @param array  $items    [page key, css class].
 */
function cafetallinn_setup_menu( $location, $name, $items ) {
	$locations = get_nav_menu_locations();
	$menu_id   = ! empty( $locations[ $location ] ) ? (int) $locations[ $location ] : 0;
	if ( ! $menu_id ) {
		$menu    = wp_get_nav_menu_object( $name );
		$menu_id = $menu ? (int) $menu->term_id : 0;
	}

	if ( ! $menu_id ) {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return;
		}
		$position = 1;
		foreach ( $items as $item ) {
			$page_id = cafetallinn_page_id( $item[0] );
			if ( ! $page_id ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-object-id' => $page_id,
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position++,
					'menu-item-classes'   => isset( $item[1] ) ? $item[1] : '',
				)
			);
		}
	} else {
		// Existing menu (live site): only add the modal classes.
		$classes = array();
		foreach ( $items as $item ) {
			if ( ! empty( $item[1] ) ) {
				$classes[ cafetallinn_page_id( $item[0] ) ] = $item[1];
			}
		}
		foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $menu_item ) {
			$object_id = (int) $menu_item->object_id;
			if ( 'page' === $menu_item->object && isset( $classes[ $object_id ] ) ) {
				$current = array_filter( (array) get_post_meta( $menu_item->ID, '_menu_item_classes', true ) );
				if ( ! in_array( $classes[ $object_id ], $current, true ) ) {
					$current[] = $classes[ $object_id ];
					update_post_meta( $menu_item->ID, '_menu_item_classes', array_values( $current ) );
				}
			}
		}
	}

	$locations              = get_nav_menu_locations();
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Primary and footer menus.
 */
function cafetallinn_setup_nav() {
	cafetallinn_setup_menu(
		'primary',
		'Peamenüü',
		array(
			array( 'avaleht' ),
			array( 'menuu', 'js-open-menu' ),
			array( 'joogikaart', 'js-open-drinks' ),
			array( 'broneeri' ),
			array( 'galerii' ),
			array( 'kontakt' ),
		)
	);
	cafetallinn_setup_menu(
		'footer',
		'Jaluse menüü',
		array(
			array( 'avaleht' ),
			array( 'menuu', 'js-open-menu' ),
			array( 'broneeri' ),
			array( 'kontakt' ),
			array( 'galerii' ),
			array( 'kkk' ),
			array( 'perekaart' ),
		)
	);
}
