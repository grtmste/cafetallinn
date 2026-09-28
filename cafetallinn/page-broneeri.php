<?php
/**
 * Template Name: Broneerimisvorm
 *
 * Booking page: the form (Contact Form 7 / WPForms shortcode) comes from
 * the page content. Also used for the Catering page.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$has_content = '' !== trim( wp_strip_all_tags( get_the_content(), true ) ) || has_shortcode( get_the_content(), 'contact-form-7' ) || has_shortcode( get_the_content(), 'wpforms' );
	$show_panel = $has_content || current_user_can( 'edit_pages' );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'booking section' . ( $show_panel ? '' : ' booking--no-form' ) ); ?>>
		<div class="container booking__grid">
			<div class="booking__head">
				<?php cafetallinn_page_header( 'page-header--left' ); ?>
			</div>
			<?php if ( $show_panel ) : ?>
				<div class="booking__form form-panel frame--brackets">
					<div class="entry-content">
						<?php
						if ( $has_content ) {
							the_content();
						} else {
							cafetallinn_form_hint();
						}
						?>
					</div>
				</div>
			<?php endif; ?>
			<aside class="booking__info" aria-label="<?php esc_attr_e( 'Kontakt ja lahtiolekuajad', 'cafetallinn' ); ?>">
				<p class="booking__lead"><?php esc_html_e( 'Eelistad helistada või kirjutada?', 'cafetallinn' ); ?></p>
				<?php cafetallinn_contact_list( false ); ?>
				<?php cafetallinn_hours( 'h2' ); ?>
			</aside>
		</div>
	</article>
	<?php
endwhile;

get_footer();
