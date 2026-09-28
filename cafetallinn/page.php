<?php
/**
 * Default page template.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article section' ); ?>>
		<div class="container container--narrow">
			<?php cafetallinn_page_header(); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="page-article__media frame frame--brackets">
					<?php the_post_thumbnail( 'large', array( 'class' => 'frame__img', 'sizes' => '(min-width: 800px) 760px, 100vw' ) ); ?>
				</figure>
			<?php endif; ?>
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Lehe osad', 'cafetallinn' ) . '">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
