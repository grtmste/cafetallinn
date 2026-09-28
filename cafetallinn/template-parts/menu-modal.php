<?php
/**
 * Menu / drink dialog.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

$cafetallinn_id    = $args['id'];
$cafetallinn_other = 'menyy' === $cafetallinn_id
	? array( 'joogikaart', __( 'Vaata ka joogikaarti', 'cafetallinn' ) )
	: array( 'menyy', __( 'Vaata ka menüüd', 'cafetallinn' ) );
?>
<div class="menu-modal" id="<?php echo esc_attr( $cafetallinn_id ); ?>" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $cafetallinn_id ); ?>-title" hidden data-menu-modal>
	<div class="menu-modal__backdrop" data-close></div>
	<div class="menu-modal__panel">
		<div class="menu-modal__head">
			<div class="menu-modal__heading">
				<p class="eyebrow">Café Tallinn</p>
				<h2 class="menu-modal__title" id="<?php echo esc_attr( $cafetallinn_id ); ?>-title"><?php echo esc_html( $args['title'] ); ?></h2>
			</div>
			<button type="button" class="menu-modal__close" data-close>
				<?php echo cafetallinn_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Sulge', 'cafetallinn' ); ?></span>
			</button>
		</div>
		<div class="menu-modal__tabs">
			<?php cafetallinn_menu_tabs( $args['type'], $cafetallinn_id ); ?>
		</div>
		<div class="menu-modal__body" data-modal-body tabindex="-1">
			<?php cafetallinn_menu_sections( $args['type'], $cafetallinn_id, 'h3' ); ?>
			<div class="menu-modal__foot">
				<?php cafetallinn_ornament(); ?>
				<a class="link-arrow" href="#<?php echo esc_attr( $cafetallinn_other[0] ); ?>" data-modal="<?php echo esc_attr( $cafetallinn_other[0] ); ?>" aria-haspopup="dialog" aria-controls="<?php echo esc_attr( $cafetallinn_other[0] ); ?>"><?php echo esc_html( $cafetallinn_other[1] ); ?> <?php echo cafetallinn_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></a>
			</div>
		</div>
	</div>
</div>
