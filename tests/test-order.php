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
     * Data Array
     * @return array
     */
    public function getDataOverPayment()
    {
        $stream = dirname(__FILE__) . '/data/callback-overpayment.json';
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
        $response = array(
            'status'                 => 'APPROVED',
    		'processorTransactionID' => 'test-processorTransactionID',
    		'errorMessage'           => '',
            'amountChanged'          => true,
            'amount'                 => "5.00",
            'originalAmount'         => $order->get_total()
        );

        \Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed($order, $response);

		$total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);

		$this->assertSame($total_paid, \floatval( $response['amount']));
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
        $response = array(
            'status'                 => 'APPROVED',
    		'processorTransactionID' => 'test-processorTransactionID',
    		'errorMessage'           => '',
            'amountChanged'          => true,
            'amount'                 => "20.00",
            'originalAmount'         => $order->get_total()
        );
        \Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed($order, $response);

        // Second callback
        $response = array(
            'status'                 => 'APPROVED',
    		'processorTransactionID' => 'test-processorTransactionID',
    		'errorMessage'           => '',
            'amountChanged'          => true,
            'amount'                 => "15.00",
            'originalAmount'         => $order->get_total()
        );
        \Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed($order, $response);

		$total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);

		$this->assertSame($total_paid, 35.00);
	}


    /**
     * Test callback for partial payment.
     *
     * @dataProvider getDataPartialPayment
     */
    public function test_callback_partial_payment($data) {
        $original_amount = floatval( $data['data']['extraData']['originalAmount'] );
        $partial_payment = floatval( $data['data']['amount'] );

        $order = $this->create_pending_payment_order($original_amount);

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
        // Get total paid.
        $total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);
        $this->assertSame($total_paid, $partial_payment);
        $this->assertSame($order_notes[0]->content, 'Order status changed from Pending payment to Partial Payment.');
    }


    /**
     * Test callback for overpayment.
     *
     * @dataProvider getDataOverPayment
     */
    public function test_callback_overpayment($data) {
        $original_amount = floatval( $data['data']['extraData']['originalAmount'] );
        $overpayment = floatval( $data['data']['amount'] );

        $order = $this->create_pending_payment_order($original_amount);

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
        $this->assertSame($order->get_status(), 'overpayment');

        $order_notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );

        // Get total paid.
        $total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);
        $this->assertSame($total_paid, $overpayment);
        $this->assertSame($order_notes[0]->content, 'Order status changed from Pending payment to Overpayment.');
    }


    /**
     * Test all cases with multiple callbacks.
     *
     * @dataProvider getDataPartialPayment
     */
    public function test_overpayment_with_multiple_callbacks($data) {
        $original_amount = floatval( $data['data']['extraData']['originalAmount'] );
        $partial_payment = floatval( $data['data']['amount'] );

        $order = $this->create_pending_payment_order($original_amount);

        // Multiple callbacks.
        for ( $i = 1; $i <= 21; $i ++ ) {
            $order_id = $order->get_id();

            // Get the callback handler.
            $callback = new \Zotapay\ApiCallback($data['stream']);

            // Single callback.
            $handle = \Zota\Zota_WooCommerce\Includes\Order::handle_callback( $order_id, $callback );

            $this->assertTrue($handle);

            // Update order meta.
            $order->add_meta_data( '_zotapay_callback', time() );
            $order->add_meta_data( '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
            $order->save();

            $order = wc_get_order( $order_id );

            $order_notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );

            // Get total paid.
            $total_paid = \Zota\Zota_WooCommerce\Includes\Order::get_total_paid($order);
            $callback_payments = floatval( $i * $partial_payment );
            $this->assertSame( $callback_payments, $total_paid );

            // Check status change
            if ( $callback_payments < $original_amount ) {
                $this->assertSame('partial-payment', $order->get_status());
            } elseif ( $callback_payments > $original_amount ) {
                $this->assertSame('overpayment', $order->get_status());
            } else {
                $this->assertSame( $original_amount, $total_paid );
                $this->assertSame( $original_amount, $callback_payments );
                $this->assertSame( 'processing', $order->get_status() );
            }
        }
    }
}
