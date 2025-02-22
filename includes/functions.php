<?php

namespace DevDay\AutoOrden;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;

function is_auto_order( \WC_Order $order ): bool {
	try {
		$checkout_fields = Package::container()->get( CheckoutFields::class );
		$meta            = (int) $checkout_fields->get_field_from_object( DEVDAY_AUTO_ORDEN_ORDER_NAMESPACE, $order, 'order' );

		return 1 === $meta;
	} catch ( \Exception $e ) {
		return false;
	}
}

function cancel_auto_order( \WC_Order $order ) {
	$order->update_meta_data( DEVDAY_AUTO_ORDEN_META_KEY, 0 );
	$order->save();
}
