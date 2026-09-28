<?php
/**
 * Template Name: Kontakt
 *
 * Contact page: address, phone, e-mail, opening hours, map and social links
 * from Customizer → Kohviku info. No contact form.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$address = cafetallinn_info( 'address' );
	$phone   = cafetallinn_info( 'phone' );
	$email   = cafetallinn_info( 'email' );
	$map     = cafetallinn_info( 'map_embed' );
	$map_url = cafetallinn_info( 'map_link' );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'contact section' ); ?>>
		<div class="container">
			<?php cafetallinn_page_header(); ?>

			<div class="contact__grid">
				<div class="contact__details">
					<?php if ( $address ) : ?>
						<div class="contact-card">
							<h2 class="contact-card__title"><?php echo cafetallinn_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'Aadress', 'cafetallinn' ); ?></h2>
							<p class="contact-card__value"><?php echo esc_html( $address ); ?></p>
							<?php if ( $map_url ) : ?>
								<p><a class="link-arrow" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ava kaardil', 'cafetallinn' ); ?> <?php echo cafetallinn_icon( 'external' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span class="screen-reader-text"><?php esc_html_e( '(avaneb uues aknas)', 'cafetallinn' ); ?></span></a></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<div class="contact-card">
							<h2 class="contact-card__title"><?php echo cafetallinn_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'Telefon', 'cafetallinn' ); ?></h2>
							<p class="contact-card__value"><a href="<?php echo esc_url( cafetallinn_tel_href( $phone ), array( 'tel' ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
						</div>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<div class="contact-card">
							<h2 class="contact-card__title"><?php echo cafetallinn_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'E-post', 'cafetallinn' ); ?></h2>
							<p class="contact-card__value"><a href="<?php echo esc_url( 'mailto:' . antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
						</div>
					<?php endif; ?>
					<div class="contact-card contact-card--hours">
						<h2 class="contact-card__title"><?php echo cafetallinn_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php esc_html_e( 'Lahtiolekuajad', 'cafetallinn' ); ?></h2>
						<?php cafetallinn_hours( 'h3' ); ?>
					</div>
					<?php if ( cafetallinn_info( 'facebook' ) || cafetallinn_info( 'instagram' ) ) : ?>
						<div class="contact-card">
							<h2 class="contact-card__title"><?php esc_html_e( 'Jälgi meid', 'cafetallinn' ); ?></h2>
							<?php cafetallinn_social(); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $map ) : ?>
					<div class="contact__map frame frame--brackets frame--brackets-alt">
						<?php if ( cafetallinn_info( 'map_autoload' ) ) : ?>
							<div class="map-facade map-facade--loaded">
								<iframe src="<?php echo esc_url( $map ); ?>" title="<?php esc_attr_e( 'Café Tallinn kaardil', 'cafetallinn' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
							</div>
						<?php else : ?>
							<div class="map-facade" data-map-src="<?php echo esc_url( $map ); ?>" data-map-title="<?php esc_attr_e( 'Café Tallinn kaardil', 'cafetallinn' ); ?>">
								<div class="map-facade__inner">
									<?php echo cafetallinn_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<p class="map-facade__title"><?php echo esc_html( $address ); ?></p>
									<button type="button" class="button" data-map-load><?php esc_html_e( 'Näita kaarti', 'cafetallinn' ); ?></button>
									<p class="map-facade__note"><?php esc_html_e( 'Kaart laaditakse Google Mapsist alles pärast klõpsu.', 'cafetallinn' ); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( '' !== trim( get_the_content() ) ) : ?>
				<div class="entry-content contact__extra">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
