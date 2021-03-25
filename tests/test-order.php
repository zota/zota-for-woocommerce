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
        $this->payment_method = ZOTA_WC_GATEWAY_ID . '_' . uniqid();
		Tests_Helper::setUp($this->payment_method);
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
	public function test_amount_changed_partial($data) {
		WC()->initialize_session();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

		// Get the callback handler.
        $callback = new \Zotapay\ApiCallback($data['stream']);

		// Check if amount changed.
		$is_amount_changed = \Zota\Zota_WooCommerce\Includes\Order::amount_changed($callback);

		$this->assertTrue( $is_amount_changed );
	}


    /**
	 * Test get order total paid for single callback.
	 */
	public function test_get_total_paid_for_single_callback() {
		WC()->initialize_session();

        $product = WC_Helper_Product::create_simple_product(
			true,
			[
				'tax_status' => 'none',
				'virtual' => true,
			]
		);

        $order = WC_Helper_Order::create_order(true, $product, $this->payment_method);
        $order->save();

        // Single callback.
        $paid = "5.00";
        \Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed($order, $paid, $order->get_total());

		$total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);

		$this->assertSame($total_paid, \floatval( $paid ));
	}

    /**
	 * Test get order total paid for multiple callbacks.
	 */
	public function test_get_total_paid_for_multiple_callbacks() {
		WC()->initialize_session();

        $product = WC_Helper_Product::create_simple_product(
			true,
			[
                'regular_price' => 100,
				'price'         => 100,
				'tax_status' => 'none',
				'virtual' => true,
			]
		);

        $order = WC_Helper_Order::create_order(true, $product, $this->payment_method);
        $order->save();

        // First callback
        \Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed($order, "20.00", "100.00");

        // Second callback
        \Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed($order, "15.00", "100.00");

		$total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);
        echo
		$this->assertSame($total_paid, 35.00);
	}
}
