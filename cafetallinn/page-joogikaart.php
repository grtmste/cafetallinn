<?php
/**
 * Template Name: Joogikaart (nimekiri)
 *
 * Full-page version of the Joogikaart dialog. The navigation link opens the
 * dialog; this page is the no-JavaScript and search engine fallback.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'menu-page section' ); ?>>
		<div class="container container--menu">
			<?php cafetallinn_page_header(); ?>
			<?php
			get_template_part(
				'template-parts/menu-list',
				null,
				array(
					'type'   => 'joogikaart',
					'prefix' => 'joogikaart-lk',
				)
			);
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
