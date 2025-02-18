<?php

namespace DevDay\AutoOrden;

use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;

class Plugin {
	private static $instance;

	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		if ( ! defined( 'DEVDAY_AUTO_ORDEN_META_KEY' ) ) {
			define( 'DEVDAY_AUTO_ORDEN_META_KEY', CheckoutFields::OTHER_FIELDS_PREFIX . DEVDAY_AUTO_ORDER_ORDER_NAMESPACE );
		}

		add_action( 'woocommerce_init', array( $this, 'add_auto_orden_checkout' ) );
		add_action( 'init', array( $this, 'schedule_midnight_check' ) );
		add_action( DEVDAY_AUTO_ORDER_MIDNIGHT_CRON_HOOK, array( $this, 'get_and_schedule_auto_orders' ) );
		add_action( DEVDAY_AUTO_ORDER_SINGLE_CRON_HOOK, array( $this, 'process_auto_orden' ), 0, 1 );
	}

	public function add_auto_orden_checkout() {
		woocommerce_register_additional_checkout_field(
			array(
				'id'            => DEVDAY_AUTO_ORDER_ORDER_NAMESPACE,
				'label'         => __( 'Auto Orden', DEVDAY_AUTO_ORDEN_SLUG ),
				'optionalLabel' => null,
				'location'      => 'order',
				'type'          => 'checkbox',
				'description'   => __( 'Marcar si deseas que tu pedido se repita automáticamente',
					DEVDAY_AUTO_ORDEN_SLUG ),
				'required'      => false,
			)
		);
	}

	public function schedule_midnight_check() {
		if ( false === as_has_scheduled_action( DEVDAY_AUTO_ORDER_MIDNIGHT_CRON_HOOK, array(), DEVDAY_AUTO_ORDER_ORDER_NAMESPACE ) ) {
			as_schedule_recurring_action(
				strtotime( 'tomorrow 1am' ),
				DAY_IN_SECONDS,
				DEVDAY_AUTO_ORDER_MIDNIGHT_CRON_HOOK,
				array(),
				DEVDAY_AUTO_ORDER_ORDER_NAMESPACE
			);
		}
	}

	public function get_and_schedule_auto_orders() {
		$order_ids = wc_get_orders(
			array(
				//'date_paid' => '<=' . strtotime( '-1 day' ),
				'meta_key'     => DEVDAY_AUTO_ORDEN_META_KEY,
				'meta_value'   => 1,
				'return'       => 'ids',
			)
		);

		foreach ( $order_ids as $order_id ) {
			$this->schedule_auto_order( $order_id );
		}
	}

	private static function schedule_auto_order( int $order_id ) {
		if ( ! as_has_scheduled_action( DEVDAY_AUTO_ORDER_SINGLE_CRON_HOOK, array( $order_id ), DEVDAY_AUTO_ORDER_ORDER_NAMESPACE ) ) {
			as_enqueue_async_action( DEVDAY_AUTO_ORDER_SINGLE_CRON_HOOK, array( $order_id ), DEVDAY_AUTO_ORDER_ORDER_NAMESPACE );
		}
	}

	public function process_auto_orden( int $order_id ): bool {
		return true;
	}
}
