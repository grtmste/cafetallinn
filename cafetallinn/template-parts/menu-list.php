<?php
/**
 * Menu / drink list on a regular page.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

$cafetallinn_type   = isset( $args['type'] ) ? $args['type'] : 'menuu';
$cafetallinn_prefix = isset( $args['prefix'] ) ? $args['prefix'] : 'menyy-lk';
?>
<div class="menu-page__tabs" data-spy-tabs>
	<?php cafetallinn_menu_tabs( $cafetallinn_type, $cafetallinn_prefix ); ?>
</div>
<div class="menu-page__body" data-spy-body>
	<?php cafetallinn_menu_sections( $cafetallinn_type, $cafetallinn_prefix, 'h2' ); ?>
</div>
