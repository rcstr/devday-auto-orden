<?php
/*
 * Plugin Name: Dev Day Auto Orden
 * Version: 1.0.0
 */

namespace DevDay\AutoOrden;

defined( 'ABSPATH' ) || exit;

define( 'DEVDAY_AUTO_ORDEN_VERSION', '1.0.0' );
define( 'DEVDAY_AUTO_ORDER_ORDER_META_KEY', 'devday/auto_order' );
define( 'DEVDAY_AUTO_ORDEN_SLUG', 'devday-auto-orden' );

require_once __DIR__ . '/vendor/autoload.php';

// check if WooCommerce is active
add_action( 'plugins_loaded', function () {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		deactivate_plugins( plugin_dir_path( __FILE__ ) . 'devday-auto-orden.php', false );
		add_action( 'admin_notices', function () {
			printf(
				'<div class="error notice is-dismissible"><p>%1$s</p></div>',
				__( 'Por favor instala WooCommerce!!',
					'devday-auto-orden' )
			);
		} );

		return;
	}

	Plugin::get_instance();
} );
