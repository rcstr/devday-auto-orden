<?php

namespace DevDay\AutoOrden;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;

function is_auto_order( \WC_Order $order ): bool {
	try {
		$checkout_fields = Package::container()->get( CheckoutFields::class );
		$meta            = (int) $checkout_fields->get_field_from_object( DEVDAY_AUTO_ORDER_ORDER_META_KEY, $order, 'order' );

		return 1 === $meta;
	} catch ( \Exception $e ) {
		return false;
	}
}
