<?php
/**
 * Café Tallinn theme bootstrap.
 *
 * @package cafetallinn
 */

defined( 'ABSPATH' ) || exit;

define( 'CAFETALLINN_VERSION', '1.0.2' );
define( 'CAFETALLINN_DIR', get_template_directory() );
define( 'CAFETALLINN_URI', get_template_directory_uri() );

require CAFETALLINN_DIR . '/inc/setup.php';
require CAFETALLINN_DIR . '/inc/enqueue.php';
require CAFETALLINN_DIR . '/inc/customizer.php';
require CAFETALLINN_DIR . '/inc/template-tags.php';
require CAFETALLINN_DIR . '/inc/menu-items.php';
require CAFETALLINN_DIR . '/inc/page-meta.php';
require CAFETALLINN_DIR . '/inc/seo.php';
require CAFETALLINN_DIR . '/inc/activation.php';
