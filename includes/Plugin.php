<?php

namespace DevDay\AutoOrden;

class Plugin {
	static private $instance;

	/**
	 * @param null $instance
	 */
	public static function get_instance() {
		if( null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	private function __construct() {
		add_action('woocommerce_init', array($this, 'add_new_field'));
	}

	public function add_new_field() {
		woocommerce_register_additional_checkout_field( array(
			'id'       => 'devday/auto-orden',
			'location' => 'order',
			'label'    => 'es una auto-orden?',
			'type'     => 'checkbox'
		) );
	}
}


