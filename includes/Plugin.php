<?php

namespace DevDay\AutoOrden;

class Plugin {
	private static $instance;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'woocommerce_init', array( $this, 'add_auto_orden_checkout' ) );
	}

	public function add_auto_orden_checkout() {
		woocommerce_register_additional_checkout_field(
			array(
				'id'            => DEVDAY_AUTO_ORDER_ORDER_META_KEY,
				'label'         => __( 'Auto Orden', DEVDAY_AUTO_ORDEN_SLUG ),
				'optionalLabel' => null,
				'location'      => 'order',
				'type'          => 'checkbox',
				'description'   => __( 'Marcar si deseas que tu pedido se repita automáticamente', DEVDAY_AUTO_ORDEN_SLUG ),
				'required'      => false,
			)
		);
	}
}
