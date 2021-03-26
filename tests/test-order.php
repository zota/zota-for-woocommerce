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
	 * Create prending payment order.
	 */
	public function create_pending_payment_order($total = 10) {
        WC()->initialize_session();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

        $total = floatval($total);

		$product = WC_Helper_Product::create_simple_product(
			true,
			[
                'regular_price' => $total,
				'price'         => $total,
				'tax_status' => 'none',
				'virtual' => true,
			]
		);

		WC()->cart->add_to_cart( $product->get_id() );

		$order = Tests_Helper::create_order( $product, 1, $this->payment_method );

		$data = [
			'code' => 200,
			'message' => null,
			'data' => [
				'merchantOrderID' => $order->get_id(),
				'orderID' => '1234',
				'depositUrl' => 'https://example.com',
			],
			'httpCode' => 200,
			'depositUrl' => 'https://example.com',
			'merchantOrderID' => $order->get_id(),
			'orderID' => '1234',
		];

		$mockResponse = [
			wp_json_encode( $data ),
			200,
		];

		\Zotapay\Zotapay::setMockResponse( $mockResponse );

		$zota = $payment_gateways[ $order->get_payment_method() ];

		$result = $zota->process_payment( $order->get_id() );

		/**
		 * Set $_GET here because the redirect request handler deals
		 * directly with it.
		 */
		$_GET = [
			'billingDescriptor' => '',
			'merchantOrderID' => $order->get_id(),
			'orderID' => '1234',
			'status' => 'PENDING',
		];

		$verify['status'] = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$verify['orderID'] = isset( $_GET['orderID'] ) ? $_GET['orderID'] : '';
		$verify['merchantOrderID'] = isset( $_GET['merchantOrderID'] ) ? $_GET['merchantOrderID'] : '';
		$verify['merchantSecretKey'] = \Zotapay\Zotapay::getMerchantSecretKey();

		$_GET['signature'] = hash( 'sha256', \implode( '', $verify ) );

		// Trigger redirect request to thank you page.
		do_action( 'woocommerce_thankyou_' . $zota->id, $order->get_id() );

		return wc_get_order( $order->get_id() );
	}


	/**
	 * Test getting extra data.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function test_get_extra_data($data) {
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

		$this->assertSame($total_paid, 35.00);
	}


    /**
     * Test callback for partial payment.
     *
     * @dataProvider getDataPartialPayment
     */
    public function test_callback_partial_payment($data) {
        $order = $this->create_pending_payment_order(100);

        // Get the callback handler.
        $callback = new \Zotapay\ApiCallback($data['stream']);

        // Single callback.
        $handle = \Zota\Zota_WooCommerce\Includes\Order::handle_callback( $order->get_id(), $callback );

        // Update order meta.
        $order->add_meta_data( '_zotapay_callback', time() );
        $order->add_meta_data( '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
        $order->save();

        $order = wc_get_order( $order->get_id() );

        // Check status change
        $this->assertTrue($handle);
        $this->assertSame($order->get_status(), 'partial-payment');

        $order_notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );
        $last_note = $order_notes[0];

        // Get total paid.
        $total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);
        $this->assertSame($total_paid, 6.00);
        $this->assertSame($last_note->content, 'Order status changed from Pending payment to Partial Payment.');
    }
}
