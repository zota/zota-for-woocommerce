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
	public static function setUp( $payment_method_id,  $currency = 'USD', $settings = array(), $payment_method = array() ) {
		update_option( 'woocommerce_currency', $currency );

		$gateway_settings = [
			'testmode' => isset($settings['testmode']) ? $settings['testmode'] : 'yes',
			'test_merchant_id' => isset($settings['test_merchant_id']) ? $settings['test_merchant_id'] : 'dummy_merchant_id',
			'test_merchant_secret_key' => isset($settings['test_merchant_secret_key']) ? $settings['test_merchant_secret_key'] : 'dummy_merchant_secret_key',
			'logging' => isset($settings['logging']) ? $settings['logging'] : 'no',
		];

		$payment_methods = [ $payment_method_id ];
		$payment_method_settings = [
			'enabled' => isset($payment_method['enabled']) ? $payment_method['enabled'] : 'yes',
			'title' => isset($payment_method['title']) ? $payment_method['title'] : 'Credit Card (Zota)',
			'description' => isset($payment_method['description']) ? $payment_method['description'] : 'Pay with your credit card via Zota.',
			'test_endpoint' => isset($payment_method['test_endpoint']) ? $payment_method['test_endpoint'] : 'dummy_endpoint',
			'endpoint' => isset($payment_method['endpoint']) ? $payment_method['endpoint'] : 'dummy_endpoint',
			'icon' => isset($payment_method['icon']) ? $payment_method['icon'] : '',
		];

		update_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', $gateway_settings );
		update_option( 'woocommerce_' . $payment_method_id . '_settings', $payment_method_settings, true );
		update_option( 'zotapay_payment_methods', $payment_methods, false );

		WC()->session = null;

		$wc_payment_gateways = WC_Payment_Gateways::instance();
		$wc_payment_gateways->init();
	}
}
