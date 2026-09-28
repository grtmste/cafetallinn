<?php
/**
 * Site header.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#F6F1E7">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Liigu sisu juurde', 'cafetallinn' ); ?></a>
<div class="scroll-sentinel" aria-hidden="true"></div>

<header class="site-header" data-site-header>
	<div class="container site-header__inner">
		<?php cafetallinn_logo( 'header' ); ?>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Peamenüü', 'cafetallinn' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-nav__list',
					'depth'          => 1,
					'fallback_cb'    => 'cafetallinn_menu_fallback',
				)
			);
			?>
		</nav>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" data-nav-toggle>
			<span class="nav-toggle__box" aria-hidden="true"><span class="nav-toggle__bar"></span><span class="nav-toggle__bar"></span><span class="nav-toggle__bar"></span></span>
			<span class="screen-reader-text" data-nav-toggle-label data-open="<?php esc_attr_e( 'Ava menüü', 'cafetallinn' ); ?>" data-close="<?php esc_attr_e( 'Sulge menüü', 'cafetallinn' ); ?>"><?php esc_html_e( 'Ava menüü', 'cafetallinn' ); ?></span>
		</button>
	</div>
</header>

<div class="mobile-nav" id="mobile-nav" hidden data-mobile-nav>
	<div class="mobile-nav__inner">
		<nav aria-label="<?php esc_attr_e( 'Peamenüü', 'cafetallinn' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-nav__list',
					'depth'          => 1,
					'fallback_cb'    => 'cafetallinn_menu_fallback',
				)
			);
			?>
		</nav>
		<div class="mobile-nav__meta">
			<?php cafetallinn_contact_list( false ); ?>
			<?php cafetallinn_social( 'social--light' ); ?>
		</div>
	</div>
</div>

<main id="main" class="site-main" tabindex="-1">
