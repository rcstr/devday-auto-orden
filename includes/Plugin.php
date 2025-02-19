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
			define( 'DEVDAY_AUTO_ORDEN_META_KEY', CheckoutFields::OTHER_FIELDS_PREFIX . DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE );
		}

		add_action( 'woocommerce_init', array( $this, 'add_auto_orden_checkout' ) );
		add_action( 'init', array( $this, 'schedule_midnight_check' ) );
		add_action( DEVDAY_AUTO_ORDEN_MIDNIGHT_CRON_HOOK, array( $this, 'get_and_schedule_auto_orders' ) );
		add_action( DEVDAY_AUTO_ORDEN_SINGLE_CRON_HOOK, array( $this, 'process_auto_orden' ), 0, 1 );

		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_auto_orders_menu_item' ) );
		add_action( 'init', array( $this, 'register_auto_orders_endpoint' ) );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_auto_orders_query_vars' ) );
		add_action( 'woocommerce_account_auto-orders_endpoint', array( $this, 'auto_orders_endpoint_content' ) );
		add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_cancel_auto_order_button' ), 10, 2 );
	}

	public function add_auto_orden_checkout() {
		woocommerce_register_additional_checkout_field(
			array(
				'id'            => DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE,
				'label'         => __( 'Auto Orden cada dia', DEVDAY_AUTO_ORDEN_SLUG ),
				'location'      => 'order',
				'type'          => 'checkbox',
				'description'   => __( 'Marcar si deseas que tu pedido se repita automáticamente', DEVDAY_AUTO_ORDEN_SLUG ),
				'required'      => false,
			)
		);
	}

	public function schedule_midnight_check() {
		if ( false === as_has_scheduled_action( DEVDAY_AUTO_ORDEN_MIDNIGHT_CRON_HOOK, array(), DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE ) ) {
			as_schedule_recurring_action( strtotime( 'tomorrow 1am' ), DAY_IN_SECONDS, DEVDAY_AUTO_ORDEN_MIDNIGHT_CRON_HOOK, array(), DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE
			);
		}
	}

	public function get_and_schedule_auto_orders() {
		$order_ids = wc_get_orders(
			array(
				//'date_paid' => '<=' . strtotime( '-1 day' ),
				'meta_key'   => DEVDAY_AUTO_ORDEN_META_KEY,
				'meta_value' => 1,
				'return'     => 'ids',
			)
		);

		foreach ( $order_ids as $order_id ) {
			$this->schedule_auto_order( $order_id );
		}
	}

	private static function schedule_auto_order( int $order_id ): void {
		if ( ! as_has_scheduled_action( DEVDAY_AUTO_ORDEN_SINGLE_CRON_HOOK, array( $order_id ), DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE ) ) {
			as_enqueue_async_action( DEVDAY_AUTO_ORDEN_SINGLE_CRON_HOOK, array( $order_id ), DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE );
		}
	}

	public function process_auto_orden( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			throw new \Exception( 'Order not found' );
		}

		$new_order = wc_create_order(
			array(
				'status'        => 'pending',
				'customer_id'   => $order->get_customer_id(),
				'created_via'   => DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE,
				'customer_note' => $order->get_customer_note(),
			)
		);

		$new_order->add_order_note(
			sprintf(
				__( 'Auto Orden creada a partir de la orden <a href="%s">#%d</a>', DEVDAY_AUTO_ORDEN_SLUG ),
				$order->get_edit_order_url(),
				$order_id
			) );

		foreach ( $order->get_items() as $item ) {
			$new_order->add_product( $item->get_product(), $item->get_quantity() );
		}
		$new_order->calculate_totals();

		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		// @todo assign payment method to new order
		if ( empty( $order->get_payment_method() ) || ! isset( $available_gateways[ $order->get_payment_method() ] ) ) {
			$new_order->payment_complete();

			return;
		}

		$new_order->set_payment_method( $available_gateways[ $order->get_payment_method() ] );
		if ( 0 === (int) $new_order->get_total() ) {
			$new_order->payment_complete();

			return;
		}

		$available_gateways[ $order->get_payment_method() ]->process_payment( $new_order->get_id() );
	}

	public function add_auto_orders_menu_item(array $items) {
		$items['auto-orders'] = __( 'Auto Ordenes', DEVDAY_AUTO_ORDEN_SLUG );

		return $items;
	}

	public function register_auto_orders_endpoint() {
		add_rewrite_endpoint( 'auto-orders', EP_ROOT | EP_PAGES );
	}

	public function add_auto_orders_query_vars( array $vars ) {
		$vars[] = 'auto-orders';

		return $vars;
	}

	public function auto_orders_endpoint_content( $current_page ) {
		$current_page    = empty( $current_page ) ? 1 : absint( $current_page );
		$customer_orders = wc_get_orders(
			array(
				'customer'   => get_current_user_id(),
				'meta_key'   => DEVDAY_AUTO_ORDEN_META_KEY,
				'meta_value' => 1,
				'page'       => $current_page,
				'paginate'   => true
			)
		);

		wc_get_template(
			'myaccount/auto-orders.php',
			array(
				'current_page'    => absint( $current_page ),
				'customer_orders' => $customer_orders,
				'has_orders'      => 0 < $customer_orders->total,
				'wp_button_class' => wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '',
			),
			'',
			DEVDAY_AUTO_ORDEN_TEMPLATES_PATH
		);
	}
}
