<?php
/**
 * Page fields: visible heading (H1) and meta description.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register meta so it is sanitized on every save path.
 */
function cafetallinn_register_page_meta() {
	register_post_meta(
		'page',
		'_cafetallinn_heading',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_pages' );
			},
		)
	);
	register_post_meta(
		'page',
		'_cafetallinn_meta_description',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_pages' );
			},
		)
	);
}
add_action( 'init', 'cafetallinn_register_page_meta' );

/**
 * Add the meta box to pages.
 */
function cafetallinn_page_meta_box() {
	add_meta_box( 'cafetallinn_page', __( 'Lehe pealkiri ja kirjeldus', 'cafetallinn' ), 'cafetallinn_page_meta_box_html', 'page', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'cafetallinn_page_meta_box' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Post.
 */
function cafetallinn_page_meta_box_html( $post ) {
	wp_nonce_field( 'cafetallinn_page_save', 'cafetallinn_page_nonce' );
	$heading = get_post_meta( $post->ID, '_cafetallinn_heading', true );
	$desc    = get_post_meta( $post->ID, '_cafetallinn_meta_description', true );
	?>
	<p>
		<label for="cafetallinn_heading"><strong><?php esc_html_e( 'Suur pealkiri lehel (H1)', 'cafetallinn' ); ?></strong></label><br>
		<input type="text" class="large-text" id="cafetallinn_heading" name="cafetallinn_heading" value="<?php echo esc_attr( $heading ); ?>">
		<br><span class="description"><?php esc_html_e( 'Tühjaks jättes kasutatakse lehe nime. Kui täidetud, kuvatakse lehe nimi väikselt pealkirja kohal.', 'cafetallinn' ); ?></span>
	</p>
	<p>
		<label for="cafetallinn_meta_description"><strong><?php esc_html_e( 'Meta kirjeldus (otsingumootoritele)', 'cafetallinn' ); ?></strong></label><br>
		<textarea class="large-text" rows="2" id="cafetallinn_meta_description" name="cafetallinn_meta_description"><?php echo esc_textarea( $desc ); ?></textarea>
		<br><span class="description"><?php esc_html_e( 'Kui kasutad SEO pluginat (nt Yoast), kasutatakse selle plugina kirjeldust.', 'cafetallinn' ); ?></span>
	</p>
	<?php
}

/**
 * Save the fields.
 *
 * @param int $post_id Post id.
 */
function cafetallinn_page_meta_save( $post_id ) {
	if ( ! isset( $_POST['cafetallinn_page_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cafetallinn_page_nonce'] ) ), 'cafetallinn_page_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['cafetallinn_heading'] ) ) {
		update_post_meta( $post_id, '_cafetallinn_heading', sanitize_text_field( wp_unslash( $_POST['cafetallinn_heading'] ) ) );
	}
	if ( isset( $_POST['cafetallinn_meta_description'] ) ) {
		update_post_meta( $post_id, '_cafetallinn_meta_description', sanitize_textarea_field( wp_unslash( $_POST['cafetallinn_meta_description'] ) ) );
	}
}
add_action( 'save_post_page', 'cafetallinn_page_meta_save' );
