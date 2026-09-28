<?php
/**
 * 404 page.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section not-found">
	<div class="container container--narrow">
		<header class="page-header">
			<p class="eyebrow">404</p>
			<h1 class="page-header__title"><?php esc_html_e( 'Seda lehte ei leitud', 'cafetallinn' ); ?></h1>
			<?php cafetallinn_ornament( 'page-header__ornament' ); ?>
		</header>
		<p class="not-found__text"><?php esc_html_e( 'Leht võib olla kolinud või aadressis on viga. Proovi mõnda neist:', 'cafetallinn' ); ?></p>
		<div class="not-found__actions">
			<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Avalehele', 'cafetallinn' ); ?></a>
			<a class="button button--ghost" href="<?php echo esc_url( cafetallinn_page_url( 'menuu', home_url( '/#menyy' ) ) ); ?>" data-modal="menyy" aria-haspopup="dialog" aria-controls="menyy"><?php esc_html_e( 'Menüü', 'cafetallinn' ); ?></a>
			<a class="button button--ghost" href="<?php echo esc_url( cafetallinn_page_url( 'kontakt' ) ); ?>"><?php esc_html_e( 'Kontakt', 'cafetallinn' ); ?></a>
		</div>
	</div>
</section>
<?php
get_footer();
