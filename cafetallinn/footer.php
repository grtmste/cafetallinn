<?php
/**
 * Site footer, menu and drink dialogs.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<?php cafetallinn_logo( 'footer' ); ?>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<p class="site-footer__tagline"><?php bloginfo( 'description' ); ?></p>
			<?php endif; ?>
			<?php cafetallinn_social( 'social--light' ); ?>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Kontakt', 'cafetallinn' ); ?></h2>
			<?php cafetallinn_contact_list(); ?>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Lahtiolekuajad', 'cafetallinn' ); ?></h2>
			<?php cafetallinn_hours( 'h3' ); ?>
		</div>

		<nav class="site-footer__col site-footer__nav" aria-label="<?php esc_attr_e( 'Jaluse menüü', 'cafetallinn' ); ?>">
			<h2 class="site-footer__title"><?php esc_html_e( 'Lehed', 'cafetallinn' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-nav__list',
					'depth'          => 1,
					'fallback_cb'    => 'cafetallinn_menu_fallback',
				)
			);
			?>
		</nav>
	</div>

	<div class="container site-footer__bottom">
		<?php cafetallinn_ornament( 'ornament--footer' ); ?>
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Café Tallinn</p>
	</div>
</footer>

<?php cafetallinn_menu_modals(); ?>

<?php wp_footer(); ?>
</body>
</html>
