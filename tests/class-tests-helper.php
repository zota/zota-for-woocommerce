<?php

/**
 * @package Zota_Woocommerce
 */

/**
 * Class Tests_Helper.
 */
class Tests_Helper {

	/**
	 * Enable payment gateways.
	 *
	 * @param  string $payment_method_id WC Payment method id.
	 * @param  string $currency       Shop currency.
	 * @param  array  $settings       ZotaPay general settings.
	 * @param  array  $payment_method Payment method settings.
	 */
	public static function setUp( $payment_method_id, $currency = 'USD', $settings = array(), $payment_method = array() ) {
		update_option( 'woocommerce_currency', $currency );

		$gateway_settings = [
			'testmode' => isset( $settings['testmode'] ) ? $settings['testmode'] : 'yes',
			'test_merchant_id' => isset( $settings['test_merchant_id'] ) ? $settings['test_merchant_id'] : 'dummy_merchant_id',
			'test_merchant_secret_key' => isset( $settings['test_merchant_secret_key'] ) ? $settings['test_merchant_secret_key'] : 'dummy_merchant_secret_key',
			'logging' => isset( $settings['logging'] ) ? $settings['logging'] : 'no',
		];

		$payment_methods = [ $payment_method_id ];
		$payment_method_settings = [
			'enabled' => isset( $payment_method['enabled'] ) ? $payment_method['enabled'] : 'yes',
			'title' => isset( $payment_method['title'] ) ? $payment_method['title'] : 'Credit Card (Zota)',
			'description' => isset( $payment_method['description'] ) ? $payment_method['description'] : 'Pay with your credit card via Zota.',
			'test_endpoint' => isset( $payment_method['test_endpoint'] ) ? $payment_method['test_endpoint'] : 'dummy_endpoint',
			'endpoint' => isset( $payment_method['endpoint'] ) ? $payment_method['endpoint'] : 'dummy_endpoint',
			'icon' => isset( $payment_method['icon'] ) ? $payment_method['icon'] : '',
		];

		update_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', $gateway_settings );
		update_option( 'woocommerce_' . $payment_method_id . '_settings', $payment_method_settings, true );
		update_option( 'zotapay_payment_methods', $payment_methods, false );

		WC()->session = null;

		$wc_payment_gateways = WC_Payment_Gateways::instance();
		$wc_payment_gateways->init();
	}


	public static function create_order( $product, $qty, $gateway ) {
		WC_Helper_Shipping::create_simple_flat_rate();

		$order_data = array(
			'status'        => 'pending',
			'customer_id'   => 1,
			'customer_note' => '',
			'total'         => '',
		);

		$_SERVER['REMOTE_ADDR'] = '127.0.0.1'; // Required, else wc_create_order throws an exception.
		$order                  = wc_create_order( $order_data );

		// Add order products.
		$item = new WC_Order_Item_Product();
		$item->set_props(
			array(
				'product'  => $product,
				'quantity' => 1,
				'subtotal' => wc_get_price_excluding_tax( $product, array( 'qty' => $qty ) ),
				'total'    => wc_get_price_excluding_tax( $product, array( 'qty' => $qty ) ),
			)
		);
		$item->save();
		$order->add_item( $item );

		// Set billing address.
		$order->set_billing_first_name( 'Jeroen' );
		$order->set_billing_last_name( 'Sormani' );
		$order->set_billing_company( 'WooCompany' );
		$order->set_billing_address_1( 'WooAddress' );
		$order->set_billing_address_2( '' );
		$order->set_billing_city( 'WooCity' );
		$order->set_billing_state( 'NY' );
		$order->set_billing_postcode( '12345' );
		$order->set_billing_country( 'US' );
		$order->set_billing_email( 'admin@example.org' );
		$order->set_billing_phone( '555-32123' );

		// Set payment gateway.
		$payment_gateways = WC()->payment_gateways->payment_gateways();

		$order->set_payment_method( $payment_gateways[ $gateway ] );

		// Set totals.
		$order->set_shipping_total( 0 );
		$order->set_discount_total( 0 );
		$order->set_discount_tax( 0 );
		$order->set_cart_tax( 0 );
		$order->set_shipping_tax( 0 );
		$order->set_total( wc_get_price_excluding_tax( $product, array( 'qty' => $qty ) ) );
		$order->save();

		return $order;
	}
}
