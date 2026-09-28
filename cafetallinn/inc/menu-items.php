<?php
/**
 * Menu items: custom post type, category taxonomy, meta fields, admin UI
 * and front-end rendering (modal + /menuu/ and /joogikaart/ pages).
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Menu types: slug => label.
 *
 * @return array
 */
function cafetallinn_menu_types() {
	return array(
		'menuu'      => __( 'Menüü', 'cafetallinn' ),
		'joogikaart' => __( 'Joogikaart', 'cafetallinn' ),
	);
}

/**
 * Tag abbreviations shown next to items, with their full meaning.
 *
 * @return array
 */
function cafetallinn_menu_tag_labels() {
	return apply_filters(
		'cafetallinn_menu_tag_labels',
		array(
			'V'  => __( 'taimetoit', 'cafetallinn' ),
			'VG' => __( 'vegan', 'cafetallinn' ),
			'GV' => __( 'gluteenivaba', 'cafetallinn' ),
			'LV' => __( 'laktoosivaba', 'cafetallinn' ),
			'PV' => __( 'piimavaba', 'cafetallinn' ),
			'T'  => __( 'terav', 'cafetallinn' ),
		)
	);
}

/**
 * Integer sanitizer for register_*_meta() (receives extra arguments).
 *
 * @param mixed $value Value.
 * @return int
 */
function cafetallinn_sanitize_int( $value ) {
	return (int) $value;
}

/**
 * Register post type and taxonomy.
 */
function cafetallinn_register_menu_cpt() {
	register_post_type(
		'menu_item',
		array(
			'labels'          => array(
				'name'               => __( 'Menüü ja joogid', 'cafetallinn' ),
				'singular_name'      => __( 'Toode', 'cafetallinn' ),
				'menu_name'          => __( 'Menüü ja joogid', 'cafetallinn' ),
				'all_items'          => __( 'Kõik tooted', 'cafetallinn' ),
				'add_new'            => __( 'Lisa uus', 'cafetallinn' ),
				'add_new_item'       => __( 'Lisa uus toode', 'cafetallinn' ),
				'edit_item'          => __( 'Muuda toodet', 'cafetallinn' ),
				'new_item'           => __( 'Uus toode', 'cafetallinn' ),
				'search_items'       => __( 'Otsi tooteid', 'cafetallinn' ),
				'not_found'          => __( 'Tooteid ei leitud', 'cafetallinn' ),
				'not_found_in_trash' => __( 'Prügikastis tooteid pole', 'cafetallinn' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_position'   => 21,
			'menu_icon'       => 'dashicons-food',
			'supports'        => array( 'title', 'page-attributes' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);

	register_taxonomy(
		'menu_category',
		'menu_item',
		array(
			'labels'            => array(
				'name'          => __( 'Kategooriad', 'cafetallinn' ),
				'singular_name' => __( 'Kategooria', 'cafetallinn' ),
				'add_new_item'  => __( 'Lisa uus kategooria', 'cafetallinn' ),
				'edit_item'     => __( 'Muuda kategooriat', 'cafetallinn' ),
				'search_items'  => __( 'Otsi kategooriaid', 'cafetallinn' ),
				'not_found'     => __( 'Kategooriaid ei leitud', 'cafetallinn' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
			'meta_box_cb'       => 'post_categories_meta_box',
		)
	);

	$meta = array(
		'_cafetallinn_type'        => 'sanitize_key',
		'_cafetallinn_price'       => 'sanitize_text_field',
		'_cafetallinn_description' => 'sanitize_textarea_field',
		'_cafetallinn_tags'        => 'sanitize_text_field',
	);
	foreach ( $meta as $key => $cb ) {
		register_post_meta(
			'menu_item',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => $cb,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
	register_term_meta(
		'menu_category',
		'_cafetallinn_order',
		array(
			'type'              => 'integer',
			'single'            => true,
			'sanitize_callback' => 'cafetallinn_sanitize_int',
		)
	);
	register_term_meta(
		'menu_category',
		'_cafetallinn_type',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_key',
		)
	);
}
add_action( 'init', 'cafetallinn_register_menu_cpt' );

/* ---------------------------------------------------------------------------
 * Item meta box
 * ------------------------------------------------------------------------ */

/**
 * Add the meta box.
 */
function cafetallinn_menu_meta_box() {
	add_meta_box( 'cafetallinn_menu_item', __( 'Toote andmed', 'cafetallinn' ), 'cafetallinn_menu_meta_box_html', 'menu_item', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'cafetallinn_menu_meta_box' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Post.
 */
function cafetallinn_menu_meta_box_html( $post ) {
	wp_nonce_field( 'cafetallinn_menu_item_save', 'cafetallinn_menu_item_nonce' );
	$type  = get_post_meta( $post->ID, '_cafetallinn_type', true );
	$price = get_post_meta( $post->ID, '_cafetallinn_price', true );
	$desc  = get_post_meta( $post->ID, '_cafetallinn_description', true );
	$tags  = get_post_meta( $post->ID, '_cafetallinn_tags', true );
	?>
	<p>
		<label for="cafetallinn_type"><strong><?php esc_html_e( 'Tüüp', 'cafetallinn' ); ?></strong></label><br>
		<select name="cafetallinn_type" id="cafetallinn_type">
			<?php foreach ( cafetallinn_menu_types() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $type, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="cafetallinn_price"><strong><?php esc_html_e( 'Hind', 'cafetallinn' ); ?></strong></label><br>
		<input type="text" class="regular-text" name="cafetallinn_price" id="cafetallinn_price" value="<?php echo esc_attr( $price ); ?>" placeholder="9.00€">
		<br><span class="description"><?php esc_html_e( 'Kirjuta nii, nagu see menüüs välja näeb, nt "9.00€" või "6.00€ / 8.00€". Tühjaks jättes hinda ei kuvata.', 'cafetallinn' ); ?></span>
	</p>
	<p>
		<label for="cafetallinn_description"><strong><?php esc_html_e( 'Kirjeldus', 'cafetallinn' ); ?></strong></label><br>
		<textarea class="large-text" rows="3" name="cafetallinn_description" id="cafetallinn_description"><?php echo esc_textarea( $desc ); ?></textarea>
	</p>
	<p>
		<label for="cafetallinn_tags"><strong><?php esc_html_e( 'Sildid', 'cafetallinn' ); ?></strong></label><br>
		<input type="text" class="regular-text" name="cafetallinn_tags" id="cafetallinn_tags" value="<?php echo esc_attr( $tags ); ?>" placeholder="V, GV">
		<br><span class="description">
			<?php
			$legend = array();
			foreach ( cafetallinn_menu_tag_labels() as $abbr => $label ) {
				$legend[] = $abbr . ' = ' . $label;
			}
			/* translators: %s: list of tag abbreviations. */
			echo esc_html( sprintf( __( 'Komaga eraldatud lühendid: %s.', 'cafetallinn' ), implode( ', ', $legend ) ) );
			?>
		</span>
	</p>
	<p class="description"><?php esc_html_e( 'Järjekord kategooria sees: paremal kastis "Järjekord" (väiksem number = eespool). Kategooria järjekorra saad muuta menüüs "Kategooriad".', 'cafetallinn' ); ?></p>
	<?php
}

/**
 * Save item meta.
 *
 * @param int $post_id Post id.
 */
function cafetallinn_menu_item_save( $post_id ) {
	if ( ! isset( $_POST['cafetallinn_menu_item_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cafetallinn_menu_item_nonce'] ) ), 'cafetallinn_menu_item_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type = isset( $_POST['cafetallinn_type'] ) ? sanitize_key( wp_unslash( $_POST['cafetallinn_type'] ) ) : 'menuu';
	if ( ! array_key_exists( $type, cafetallinn_menu_types() ) ) {
		$type = 'menuu';
	}
	update_post_meta( $post_id, '_cafetallinn_type', $type );
	update_post_meta( $post_id, '_cafetallinn_price', isset( $_POST['cafetallinn_price'] ) ? sanitize_text_field( wp_unslash( $_POST['cafetallinn_price'] ) ) : '' );
	update_post_meta( $post_id, '_cafetallinn_description', isset( $_POST['cafetallinn_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cafetallinn_description'] ) ) : '' );
	update_post_meta( $post_id, '_cafetallinn_tags', isset( $_POST['cafetallinn_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['cafetallinn_tags'] ) ) : '' );
}
add_action( 'save_post_menu_item', 'cafetallinn_menu_item_save' );

/* ---------------------------------------------------------------------------
 * Category fields (order + type)
 * ------------------------------------------------------------------------ */

/**
 * Fields on "Add category".
 */
function cafetallinn_term_add_fields() {
	wp_nonce_field( 'cafetallinn_term_save', 'cafetallinn_term_nonce' );
	?>
	<div class="form-field">
		<label for="cafetallinn_term_type"><?php esc_html_e( 'Kuulub', 'cafetallinn' ); ?></label>
		<select name="cafetallinn_term_type" id="cafetallinn_term_type">
			<?php foreach ( cafetallinn_menu_types() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form-field">
		<label for="cafetallinn_term_order"><?php esc_html_e( 'Järjekord', 'cafetallinn' ); ?></label>
		<input type="number" name="cafetallinn_term_order" id="cafetallinn_term_order" value="0">
		<p><?php esc_html_e( 'Väiksem number = eespool (menüü vahekaartides ja nimekirjas).', 'cafetallinn' ); ?></p>
	</div>
	<?php
}
add_action( 'menu_category_add_form_fields', 'cafetallinn_term_add_fields' );

/**
 * Fields on "Edit category".
 *
 * @param WP_Term $term Term.
 */
function cafetallinn_term_edit_fields( $term ) {
	$order = (int) get_term_meta( $term->term_id, '_cafetallinn_order', true );
	$type  = get_term_meta( $term->term_id, '_cafetallinn_type', true );
	wp_nonce_field( 'cafetallinn_term_save', 'cafetallinn_term_nonce' );
	?>
	<tr class="form-field">
		<th scope="row"><label for="cafetallinn_term_type"><?php esc_html_e( 'Kuulub', 'cafetallinn' ); ?></label></th>
		<td><select name="cafetallinn_term_type" id="cafetallinn_term_type">
			<?php foreach ( cafetallinn_menu_types() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $type, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="cafetallinn_term_order"><?php esc_html_e( 'Järjekord', 'cafetallinn' ); ?></label></th>
		<td><input type="number" name="cafetallinn_term_order" id="cafetallinn_term_order" value="<?php echo esc_attr( $order ); ?>">
		<p class="description"><?php esc_html_e( 'Väiksem number = eespool.', 'cafetallinn' ); ?></p></td>
	</tr>
	<?php
}
add_action( 'menu_category_edit_form_fields', 'cafetallinn_term_edit_fields' );

/**
 * Save category fields.
 *
 * @param int $term_id Term id.
 */
function cafetallinn_term_save( $term_id ) {
	if ( ! isset( $_POST['cafetallinn_term_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cafetallinn_term_nonce'] ) ), 'cafetallinn_term_save' )
		|| ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['cafetallinn_term_order'] ) ) {
		update_term_meta( $term_id, '_cafetallinn_order', intval( wp_unslash( $_POST['cafetallinn_term_order'] ) ) );
	}
	if ( isset( $_POST['cafetallinn_term_type'] ) ) {
		$type = sanitize_key( wp_unslash( $_POST['cafetallinn_term_type'] ) );
		if ( array_key_exists( $type, cafetallinn_menu_types() ) ) {
			update_term_meta( $term_id, '_cafetallinn_type', $type );
		}
	}
}
add_action( 'created_menu_category', 'cafetallinn_term_save' );
add_action( 'edited_menu_category', 'cafetallinn_term_save' );

/**
 * Order column on the categories screen.
 *
 * @param array $cols Columns.
 * @return array
 */
function cafetallinn_term_columns( $cols ) {
	$cols['cafetallinn_type']  = __( 'Kuulub', 'cafetallinn' );
	$cols['cafetallinn_order'] = __( 'Järjekord', 'cafetallinn' );
	unset( $cols['description'] );
	return $cols;
}
add_filter( 'manage_edit-menu_category_columns', 'cafetallinn_term_columns' );

/**
 * Column values.
 *
 * @param string $out     Output.
 * @param string $column  Column.
 * @param int    $term_id Term id.
 * @return string
 */
function cafetallinn_term_column_value( $out, $column, $term_id ) {
	if ( 'cafetallinn_order' === $column ) {
		return esc_html( (string) (int) get_term_meta( $term_id, '_cafetallinn_order', true ) );
	}
	if ( 'cafetallinn_type' === $column ) {
		$types = cafetallinn_menu_types();
		$type  = get_term_meta( $term_id, '_cafetallinn_type', true );
		return esc_html( isset( $types[ $type ] ) ? $types[ $type ] : '—' );
	}
	return $out;
}
add_filter( 'manage_menu_category_custom_column', 'cafetallinn_term_column_value', 10, 3 );

/* ---------------------------------------------------------------------------
 * Item list screen
 * ------------------------------------------------------------------------ */

/**
 * Columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function cafetallinn_item_columns( $cols ) {
	$new = array();
	foreach ( $cols as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['cafetallinn_price'] = __( 'Hind', 'cafetallinn' );
			$new['cafetallinn_type']  = __( 'Tüüp', 'cafetallinn' );
			$new['menu_order']        = __( 'Järjekord', 'cafetallinn' );
		}
	}
	unset( $new['date'] );
	return $new;
}
add_filter( 'manage_menu_item_posts_columns', 'cafetallinn_item_columns' );

/**
 * Column values.
 *
 * @param string $column  Column.
 * @param int    $post_id Post id.
 */
function cafetallinn_item_column_value( $column, $post_id ) {
	if ( 'cafetallinn_price' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_cafetallinn_price', true ) );
	} elseif ( 'cafetallinn_type' === $column ) {
		$types = cafetallinn_menu_types();
		$type  = get_post_meta( $post_id, '_cafetallinn_type', true );
		echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : '—' );
	} elseif ( 'menu_order' === $column ) {
		echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
	}
}
add_action( 'manage_menu_item_posts_custom_column', 'cafetallinn_item_column_value', 10, 2 );

/**
 * Sortable order column.
 *
 * @param array $cols Columns.
 * @return array
 */
function cafetallinn_item_sortable( $cols ) {
	$cols['menu_order'] = 'menu_order';
	return $cols;
}
add_filter( 'manage_edit-menu_item_sortable_columns', 'cafetallinn_item_sortable' );

/**
 * Type and category filters above the list.
 *
 * @param string $post_type Post type.
 */
function cafetallinn_item_filters( $post_type ) {
	if ( 'menu_item' !== $post_type ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	$current = isset( $_GET['cafetallinn_type'] ) ? sanitize_key( wp_unslash( $_GET['cafetallinn_type'] ) ) : '';
	echo '<select name="cafetallinn_type"><option value="">' . esc_html__( 'Kõik tüübid', 'cafetallinn' ) . '</option>';
	foreach ( cafetallinn_menu_types() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
	wp_dropdown_categories(
		array(
			'taxonomy'        => 'menu_category',
			'name'            => 'menu_category',
			'value_field'     => 'slug',
			'show_option_all' => __( 'Kõik kategooriad', 'cafetallinn' ),
			'hide_empty'      => false,
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
			'selected'        => isset( $_GET['menu_category'] ) ? sanitize_title( wp_unslash( $_GET['menu_category'] ) ) : '',
		)
	);
}
add_action( 'restrict_manage_posts', 'cafetallinn_item_filters' );

/**
 * Apply the type filter and default ordering on the list screen.
 *
 * @param WP_Query $query Query.
 */
function cafetallinn_item_admin_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'menu_item' !== $query->get( 'post_type' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	$type = isset( $_GET['cafetallinn_type'] ) ? sanitize_key( wp_unslash( $_GET['cafetallinn_type'] ) ) : '';
	if ( $type ) {
		$query->set( 'meta_key', '_cafetallinn_type' );
		$query->set( 'meta_value', $type );
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list filter.
	$cat = isset( $_GET['menu_category'] ) ? sanitize_title( wp_unslash( $_GET['menu_category'] ) ) : '';
	if ( $cat && '0' !== $cat ) {
		$query->set(
			'tax_query',
			array(
				array(
					'taxonomy' => 'menu_category',
					'field'    => 'slug',
					'terms'    => $cat,
				),
			)
		);
	}
	if ( ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'cafetallinn_item_admin_query' );

/* ---------------------------------------------------------------------------
 * Data + rendering
 * ------------------------------------------------------------------------ */

/**
 * Items of one type grouped by category, categories in their set order.
 *
 * @param string $type menuu|joogikaart.
 * @return array[] Each: term, items[].
 */
function cafetallinn_menu_data( $type ) {
	static $cache = array();
	if ( isset( $cache[ $type ] ) ) {
		return $cache[ $type ];
	}

	$posts = get_posts(
		array(
			'post_type'        => 'menu_item',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'meta_key'         => '_cafetallinn_type', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_key
			'meta_value'       => $type, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);

	$groups = array();
	foreach ( $posts as $post ) {
		$terms = get_the_terms( $post, 'menu_category' );
		$term  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
		$key   = $term ? $term->term_id : 0;
		if ( ! isset( $groups[ $key ] ) ) {
			$groups[ $key ] = array(
				'term'  => $term,
				'order' => $term ? (int) get_term_meta( $term->term_id, '_cafetallinn_order', true ) : PHP_INT_MAX,
				'items' => array(),
			);
		}
		$tags = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $post->ID, '_cafetallinn_tags', true ) ) ) );

		$groups[ $key ]['items'][] = array(
			'name'        => get_the_title( $post ),
			'price'       => get_post_meta( $post->ID, '_cafetallinn_price', true ),
			'description' => get_post_meta( $post->ID, '_cafetallinn_description', true ),
			'tags'        => $tags,
		);
	}
	uasort(
		$groups,
		function ( $a, $b ) {
			return $a['order'] <=> $b['order'];
		}
	);

	$cache[ $type ] = array_values( $groups );
	return $cache[ $type ];
}

/**
 * Section id for a category inside a list.
 *
 * @param string       $prefix List prefix.
 * @param WP_Term|null $term   Term.
 * @return string
 */
function cafetallinn_menu_section_id( $prefix, $term ) {
	return $prefix . '-' . ( $term ? $term->slug : 'muu' );
}

/**
 * Category navigation (tabs / chips).
 *
 * @param string $type   Menu type.
 * @param string $prefix Id prefix.
 */
function cafetallinn_menu_tabs( $type, $prefix ) {
	$groups = cafetallinn_menu_data( $type );
	if ( count( $groups ) < 2 ) {
		return;
	}
	echo '<nav class="menu-tabs" aria-label="' . esc_attr__( 'Kategooriad', 'cafetallinn' ) . '"><ul class="menu-tabs__list">';
	foreach ( $groups as $group ) {
		$label = $group['term'] ? $group['term']->name : __( 'Muu', 'cafetallinn' );
		printf(
			'<li><a class="menu-tabs__link" href="#%1$s">%2$s</a></li>',
			esc_attr( cafetallinn_menu_section_id( $prefix, $group['term'] ) ),
			esc_html( $label )
		);
	}
	echo '</ul></nav>';
}

/**
 * Category sections with item rows.
 *
 * @param string $type          Menu type.
 * @param string $prefix        Id prefix.
 * @param string $heading_level h2|h3.
 */
function cafetallinn_menu_sections( $type, $prefix, $heading_level = 'h3' ) {
	$groups = cafetallinn_menu_data( $type );
	$tag    = tag_escape( $heading_level );
	$labels = cafetallinn_menu_tag_labels();

	if ( ! $groups ) {
		echo '<p class="menu-empty">' . esc_html__( 'Menüü on hetkel uuendamisel.', 'cafetallinn' ) . '</p>';
		return;
	}

	foreach ( $groups as $group ) {
		$label = $group['term'] ? $group['term']->name : __( 'Muu', 'cafetallinn' );
		$id    = cafetallinn_menu_section_id( $prefix, $group['term'] );
		$desc  = $group['term'] ? term_description( $group['term'] ) : '';
		$wide  = count( $group['items'] ) > 3 ? ' menu-section--split' : '';

		echo '<section class="menu-section' . esc_attr( $wide ) . '" id="' . esc_attr( $id ) . '" aria-labelledby="' . esc_attr( $id ) . '-title">';
		echo '<header class="menu-section__header"><' . $tag . ' class="menu-section__title" id="' . esc_attr( $id ) . '-title">' . esc_html( $label ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag_escape().
		if ( $desc ) {
			echo '<div class="menu-section__desc">' . wp_kses_post( $desc ) . '</div>';
		}
		echo '</header><ul class="menu-list">';

		foreach ( $group['items'] as $item ) {
			echo '<li class="menu-row' . ( '' === $item['price'] ? ' menu-row--noprice' : '' ) . '"><div class="menu-row__head">';
			echo '<span class="menu-row__name">' . esc_html( $item['name'] );
			if ( $item['tags'] ) {
				echo '<span class="menu-row__tags">';
				foreach ( $item['tags'] as $abbr ) {
					$title = isset( $labels[ $abbr ] ) ? $labels[ $abbr ] : $abbr;
					echo ' <abbr class="menu-tag" title="' . esc_attr( $title ) . '">' . esc_html( $abbr ) . '</abbr>';
				}
				echo '</span>';
			}
			echo '</span>';
			if ( '' !== $item['price'] ) {
				echo '<span class="menu-row__leader" aria-hidden="true"></span><span class="menu-row__price">' . esc_html( $item['price'] ) . '</span>';
			}
			echo '</div>';
			if ( $item['description'] ) {
				echo '<p class="menu-row__desc">' . esc_html( $item['description'] ) . '</p>';
			}
			echo '</li>';
		}
		echo '</ul></section>';
	}
}

/**
 * The two dialogs, printed once in the footer.
 */
function cafetallinn_menu_modals() {
	$modals = array(
		'menyy'      => array( 'menuu', __( 'Menüü', 'cafetallinn' ) ),
		'joogikaart' => array( 'joogikaart', __( 'Joogikaart', 'cafetallinn' ) ),
	);
	foreach ( $modals as $id => $modal ) {
		list( $type, $title ) = $modal;
		get_template_part(
			'template-parts/menu-modal',
			null,
			array(
				'id'    => $id,
				'type'  => $type,
				'title' => $title,
			)
		);
	}
}
