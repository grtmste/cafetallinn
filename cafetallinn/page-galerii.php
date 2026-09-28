<?php
/**
 * Template Name: Galerii
 *
 * Gallery page: the images come from the gallery block in the page content,
 * laid out as a masonry grid with a keyboard / swipe lightbox.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery-page section' ); ?>>
		<div class="container">
			<?php cafetallinn_page_header(); ?>
			<div class="entry-content gallery-page__content" data-lightbox-group>
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
