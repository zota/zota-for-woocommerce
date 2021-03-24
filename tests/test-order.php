<?php

/**
 * @package Zota_Woocommerce
 */

/**
 * Class WC_Tests_Payment_Gateway.
 */
class WC_Tests_Order extends WC_Unit_Test_Case {

	/**
     * Data Array
     * @return array
     */
    public function getDataPartialPayment()
    {
        $stream = dirname(__FILE__) . '/data/callback-partial-payment.json';
        $fileContents = \file_get_contents($stream);
        $data = \json_decode($fileContents, JSON_OBJECT_AS_ARRAY);

        return [
            [
                [
                    'stream' => $stream,
                    'data' => $data,
                ],
            ],
        ];
    }


	/**
	 * Setup, enable payment gateways.
	 */
	public function setUp() {
		parent::setUp();

		/**
		 * TODO: We should probably move these in a global helper / setUp method.
		 */
		update_option( 'woocommerce_currency', 'USD' );

		$settings = [
			'testmode' => 'yes',
			'test_merchant_id' => 'dummy_merchant_id',
			'test_merchant_secret_key' => 'dummy_merchant_secret_key',
			'logging' => 'no',
		];

		$payment_method_id = ZOTA_WC_GATEWAY_ID . '_' . uniqid();
		$payment_methods = [ $payment_method_id ];
		$payment_method_settings = [
			'enabled' => 'yes',
			'title' => 'Credit Card (Zota)',
			'description' => 'Pay with your credit card via Zota.',
			'test_endpoint' => 'dummy_endpoint',
			'endpoint' => 'dummy_endpoint',
			'icon' => '',
		];

		update_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', $settings );
		update_option( 'woocommerce_' . $payment_method_id . '_settings', $payment_method_settings, true );
		update_option( 'zotapay_payment_methods', $payment_methods, false );

		$this->payment_method = $payment_method_id;

		WC()->session = null;

		$wc_payment_gateways = WC_Payment_Gateways::instance();
		$wc_payment_gateways->init();
	}


	/**
	 * Initialize session that some tests might have removed.
	 */
	public function tearDown() {
		parent::tearDown();
		WC()->initialize_session();
	}


	/**
	 * Test getting extra data.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function test_get_extra_data($data) {
		WC()->initialize_session();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

		// Get the callback handler.
        $callback = new \Zotapay\ApiCallback($data['stream']);

		// Get extra data.
		$extra_data = \Zota\Zota_WooCommerce\Includes\Order::get_extra_data($callback);

		$this->assertTrue( is_array( $extra_data ) );
	}


	/**
	 * Test if amount changed.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function amount_changed_partial($data) {
		WC()->initialize_session();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

		// Get the callback handler.
        $callback = new \Zotapay\ApiCallback($data['stream']);

		// Check if amount changed.
		$is_amount_changed = \Zota\Zota_WooCommerce\Includes\Order::amount_changed($callback);

		$this->assertTrue( $is_amount_changed );
	}
}
