<?php
/**
 * Fallback template (posts, archives, search).
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="section">
	<div class="container container--narrow">
		<?php if ( is_singular() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
					<header class="page-header">
						<p class="eyebrow"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
						<h1 class="page-header__title"><?php the_title(); ?></h1>
						<?php cafetallinn_ornament( 'page-header__ornament' ); ?>
					</header>
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="page-article__media frame frame--brackets"><?php the_post_thumbnail( 'large', array( 'class' => 'frame__img' ) ); ?></figure>
					<?php endif; ?>
					<div class="entry-content">
						<?php
						the_content();
						wp_link_pages();
						?>
					</div>
					<footer class="entry-meta"><?php the_tags( '<p class="entry-tags">', ', ', '</p>' ); ?></footer>
				</article>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		<?php else : ?>
			<header class="page-header">
				<h1 class="page-header__title">
					<?php
					if ( is_search() ) {
						/* translators: %s: search query. */
						printf( esc_html__( 'Otsingu tulemused: %s', 'cafetallinn' ), esc_html( get_search_query() ) );
					} elseif ( is_archive() ) {
						echo wp_kses_post( get_the_archive_title() );
					} else {
						esc_html_e( 'Uudised', 'cafetallinn' );
					}
					?>
				</h1>
				<?php cafetallinn_ornament( 'page-header__ornament' ); ?>
			</header>
			<?php if ( have_posts() ) : ?>
				<div class="post-list">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-list__item' ); ?>>
							<h2 class="post-list__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="entry-content"><?php the_excerpt(); ?></div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php
				the_posts_pagination(
					array(
						'prev_text' => __( 'Eelmised', 'cafetallinn' ),
						'next_text' => __( 'Järgmised', 'cafetallinn' ),
					)
				);
				?>
			<?php else : ?>
				<p><?php esc_html_e( 'Midagi ei leitud.', 'cafetallinn' ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
