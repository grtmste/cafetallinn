<?php
/**
 * Front page (Avaleht).
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$hero_id   = get_post_thumbnail_id();
	$hero_id   = $hero_id ? $hero_id : cafetallinn_seed_image_id( 'ANF_6945.jpg' );
	$hero2_id  = cafetallinn_image_id( 'hero_image_2', 'ANF_6902.jpg' );
	$story_id  = cafetallinn_seed_image_id( 'ANF_6914.jpg' );
	$visit_id  = cafetallinn_seed_image_id( 'ANF_6925.jpg' );
	$address   = cafetallinn_info( 'address' );
	$cafe_rows = cafetallinn_hours_rows( cafetallinn_info( 'hours_cafe' ) );
	?>

	<section class="hero" aria-labelledby="hero-title">
		<div class="container hero__grid">
			<div class="hero__text">
				<?php if ( $address ) : ?>
					<p class="eyebrow"><?php echo esc_html( $address ); ?></p>
				<?php endif; ?>
				<h1 class="hero__title" id="hero-title">Café <em>Tallinn</em></h1>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="hero__tagline"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>
				<?php cafetallinn_ornament( 'ornament--left' ); ?>
				<div class="hero__actions">
					<a class="button" href="<?php echo esc_url( cafetallinn_page_url( 'menuu', home_url( '/#menyy' ) ) ); ?>" data-modal="menyy" aria-haspopup="dialog" aria-controls="menyy"><?php esc_html_e( 'Vaata menüüd', 'cafetallinn' ); ?></a>
					<a class="button button--ghost" href="<?php echo esc_url( cafetallinn_page_url( 'broneeri' ) ); ?>"><?php esc_html_e( 'Broneeri', 'cafetallinn' ); ?></a>
				</div>
				<?php if ( $cafe_rows ) : ?>
					<p class="hero__hours">
						<?php echo cafetallinn_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<span>
							<?php
							$parts = array();
							foreach ( $cafe_rows as $row ) {
								$parts[] = trim( $row[0] . ' ' . $row[1] );
							}
							echo esc_html( implode( ' · ', $parts ) );
							?>
						</span>
					</p>
				<?php endif; ?>
			</div>

			<?php if ( $hero_id ) : ?>
				<figure class="hero__media frame frame--brackets">
					<?php
					echo wp_get_attachment_image(
						$hero_id,
						'2048x2048',
						false,
						array(
							'class'         => 'frame__img',
							'sizes'         => '(min-width: 1024px) 60vw, 100vw',
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<?php if ( $hero2_id ) : ?>
				<figure class="hero__media-2 frame">
					<?php
					echo wp_get_attachment_image(
						$hero2_id,
						'medium_large',
						false,
						array(
							'class' => 'frame__img',
							'sizes' => '(min-width: 1024px) 22vw, 40vw',
						)
					);
					?>
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<section class="story section" id="meie-lugu">
		<div class="container story__grid">
			<div class="story__content entry-content">
				<?php the_content(); ?>
			</div>
			<?php if ( $story_id ) : ?>
				<figure class="story__media frame frame--brackets frame--brackets-alt">
					<?php
					echo wp_get_attachment_image(
						$story_id,
						'large',
						false,
						array(
							'class' => 'frame__img',
							'sizes' => '(min-width: 1024px) 34vw, 100vw',
						)
					);
					?>
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<section class="explore section section--tight" aria-labelledby="explore-title">
		<div class="container">
			<h2 class="screen-reader-text" id="explore-title"><?php esc_html_e( 'Avasta', 'cafetallinn' ); ?></h2>
			<?php
			$tiles = array(
				'tile_menu_show'     => array( __( 'Menüü', 'cafetallinn' ), __( 'Ava menüü', 'cafetallinn' ), cafetallinn_page_url( 'menuu', home_url( '/#menyy' ) ), 'menyy', cafetallinn_image_id( 'tile_menu_image', 'ANF_6726.jpg' ) ),
				'tile_drinks_show'   => array( __( 'Joogikaart', 'cafetallinn' ), __( 'Ava joogikaart', 'cafetallinn' ), cafetallinn_page_url( 'joogikaart', home_url( '/#joogikaart' ) ), 'joogikaart', cafetallinn_image_id( 'tile_drinks_image', 'ANF_6909.jpg' ) ),
				'tile_events_show'   => array( __( 'Sündmused', 'cafetallinn' ), __( 'Broneeri', 'cafetallinn' ), cafetallinn_page_url( 'broneeri' ), '', cafetallinn_image_id( 'tile_events_image', 'ANF_6945.jpg' ) ),
				'tile_catering_show' => array( __( 'Catering', 'cafetallinn' ), __( 'Küsi pakkumist', 'cafetallinn' ), cafetallinn_page_url( 'catering' ), '', cafetallinn_image_id( 'tile_catering_image', 'ANF_6817.jpg' ) ),
			);
			$tiles = array_values(
				array_filter(
					$tiles,
					function ( $key ) {
						return (bool) cafetallinn_info( $key );
					},
					ARRAY_FILTER_USE_KEY
				)
			);
			if ( $tiles ) :
				?>
			<ul class="tiles tiles--<?php echo esc_attr( (string) count( $tiles ) ); ?>">
				<?php
				foreach ( $tiles as $i => $tile ) :
					list( $label, $cta, $url, $modal, $img ) = $tile;
					?>
					<li class="tile tile--<?php echo esc_attr( (string) ( $i + 1 ) ); ?>">
						<a class="tile__link" href="<?php echo esc_url( $url ); ?>"<?php echo $modal ? ' data-modal="' . esc_attr( $modal ) . '" aria-haspopup="dialog" aria-controls="' . esc_attr( $modal ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inline. ?>>
							<span class="tile__media frame">
								<?php
								if ( $img ) {
									echo wp_get_attachment_image(
										$img,
										'cafetallinn-tile',
										false,
										array(
											'class' => 'frame__img',
											'sizes' => '(min-width: 1024px) 24vw, (min-width: 600px) 46vw, 100vw',
											'alt'   => '',
										)
									);
								}
								?>
							</span>
							<span class="tile__text">
								<span class="tile__label"><?php echo esc_html( $label ); ?></span>
								<span class="tile__cta"><?php echo esc_html( $cta ); ?> <?php echo cafetallinn_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</section>

	<section class="visit" aria-labelledby="visit-title">
		<div class="container visit__grid">
			<?php if ( $visit_id ) : ?>
				<figure class="visit__media frame frame--brackets frame--on-dark">
					<?php
					echo wp_get_attachment_image(
						$visit_id,
						'large',
						false,
						array(
							'class' => 'frame__img',
							'sizes' => '(min-width: 1024px) 40vw, 100vw',
						)
					);
					?>
				</figure>
			<?php endif; ?>
			<div class="visit__text">
				<p class="eyebrow eyebrow--on-dark"><?php esc_html_e( 'Külasta meid', 'cafetallinn' ); ?></p>
				<h2 class="visit__title" id="visit-title"><?php esc_html_e( 'Lahtiolekuajad', 'cafetallinn' ); ?></h2>
				<?php cafetallinn_hours( 'h3' ); ?>
				<?php cafetallinn_contact_list(); ?>
				<div class="visit__actions">
					<a class="button button--gold" href="<?php echo esc_url( cafetallinn_page_url( 'broneeri' ) ); ?>"><?php esc_html_e( 'Broneeri', 'cafetallinn' ); ?></a>
					<a class="button button--ghost-light" href="<?php echo esc_url( cafetallinn_page_url( 'kontakt' ) ); ?>"><?php esc_html_e( 'Kontakt ja kaart', 'cafetallinn' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<?php
	$teaser = array( 'ANF_6909.jpg', 'ANF_6790.jpg', 'ANF_6927.jpg', 'ANF_6649.jpg', 'ANF_6916.jpg' );
	$teaser = array_filter( array_map( 'cafetallinn_seed_image_id', $teaser ) );
	if ( count( $teaser ) >= 3 ) :
		?>
		<section class="teaser section" aria-labelledby="teaser-title">
			<div class="container">
				<div class="section-head">
					<h2 class="section-head__title" id="teaser-title"><?php esc_html_e( 'Galerii', 'cafetallinn' ); ?></h2>
					<a class="link-arrow" href="<?php echo esc_url( cafetallinn_page_url( 'galerii' ) ); ?>"><?php esc_html_e( 'Vaata kõiki pilte', 'cafetallinn' ); ?> <?php echo cafetallinn_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></a>
				</div>
				<div class="teaser__grid" data-lightbox-group>
					<?php foreach ( array_values( $teaser ) as $i => $img ) : ?>
						<a class="teaser__item teaser__item--<?php echo esc_attr( (string) ( $i + 1 ) ); ?> frame" href="<?php echo esc_url( wp_get_attachment_image_url( $img, 'full' ) ); ?>" data-lightbox>
							<?php
							echo wp_get_attachment_image(
								$img,
								'large',
								false,
								array(
									'class' => 'frame__img',
									'sizes' => '(min-width: 1024px) 33vw, 50vw',
								)
							);
							?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
