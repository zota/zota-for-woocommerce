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
	 *
	 * @return array
	 */
	public function getDataPartialPayment() {
		$stream = dirname( __FILE__ ) . '/data/callback-partial-payment.json';
		$fileContents = \file_get_contents( $stream );
		$data = \json_decode( $fileContents, JSON_OBJECT_AS_ARRAY );

		return array(
			array(
				array(
					'stream' => $stream,
					'data' => $data,
				),
			),
		);
	}


	/**
	 * Data Array
	 *
	 * @return array
	 */
	public function getDataOverPayment() {
		$stream = dirname( __FILE__ ) . '/data/callback-overpayment.json';
		$fileContents = \file_get_contents( $stream );
		$data = \json_decode( $fileContents, JSON_OBJECT_AS_ARRAY );

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
		Tests_Helper::setUp( $this->payment_method );
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
	public function create_pending_payment_order( $total = 10 ) {
		WC()->initialize_session();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

		$total = floatval( $total );

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
	public function test_get_extra_data( $data ) {
		// Get the callback handler.
		$callback = new \Zotapay\ApiCallback( $data['stream'] );

		// Get extra data.
		$extra_data = \Zota\Zota_WooCommerce\Includes\Order::get_extra_data( $callback );

		$this->assertTrue( is_array( $extra_data ) );
	}


	/**
	 * Test if amount changed.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function test_amount_changed( $data ) {
		// Get the callback handler.
		$callback = new \Zotapay\ApiCallback( $data['stream'] );

		// Check if amount changed.
		$is_amount_changed = \Zota\Zota_WooCommerce\Includes\Order::amount_changed( $callback );

		$this->assertTrue( $is_amount_changed );
	}


	/**
	 * Test handle amount changed.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function test_handle_amount_changed( $data ) {
		$original_amount = floatval( $data['data']['extraData']['originalAmount'] );
		$partial_payment = floatval( $data['data']['amount'] );

		$order = $this->create_pending_payment_order( $original_amount );

		// Get the callback handler.
		$callback = new \Zotapay\ApiCallback( $data['stream'] );

		$response['status']                 = $callback->getStatus();
		$response['processorTransactionID'] = $callback->getProcessorTransactionID();
		$response['errorMessage']           = $callback->getErrorMessage();

		$extra_data = $callback->getExtraData();

		$response['amountChanged']  = true;
		$response['amount']         = $callback->getAmount();
		$response['originalAmount'] = $extra_data['originalAmount'];

		// Handle amount changed.
		\Zota\Zota_WooCommerce\Includes\Order::handle_amount_changed( $order, $response );

		$this->assertSame( $order->get_meta( '_zotapay_amount_changed', true ), 'yes' );
		$this->assertSame( \floatval( $order->get_meta( '_zotapay_amount', true ) ), $partial_payment );
	}


	/**
	 * Test callback for partial payment.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function test_callback_partial_payment( $data ) {
		$original_amount = floatval( $data['data']['extraData']['originalAmount'] );
		$partial_payment = floatval( $data['data']['amount'] );

		$order = $this->create_pending_payment_order( $original_amount );

		// Get the callback handler.
		$callback = new \Zotapay\ApiCallback( $data['stream'] );

		// Single callback.
		$handle = \Zota\Zota_WooCommerce\Includes\Order::handle_callback( $order->get_id(), $callback );

		// Update order meta.
		$order->add_meta_data( '_zotapay_callback', time() );
		$order->add_meta_data( '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
		$order->save();

		$order = wc_get_order( $order->get_id() );

		// Check status change
		$this->assertTrue( $handle );
		$this->assertSame( $order->get_status(), 'partial-payment' );

		$order_notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );

		// ZotaPay amount.
		$zotapay_amount = \floatval( $order->get_meta( '_zotapay_amount', true ) );

		$this->assertSame( $zotapay_amount, $partial_payment );
		$this->assertSame( $order_notes[0]->content, 'Order status changed from Pending payment to Partial Payment.' );
	}


	/**
	 * Test callback for overpayment.
	 *
	 * @dataProvider getDataOverPayment
	 */
	public function test_callback_overpayment( $data ) {
		$original_amount = floatval( $data['data']['extraData']['originalAmount'] );
		$overpayment = floatval( $data['data']['amount'] );

		$order = $this->create_pending_payment_order( $original_amount );

		// Get the callback handler.
		$callback = new \Zotapay\ApiCallback( $data['stream'] );

		// Single callback.
		$handle = \Zota\Zota_WooCommerce\Includes\Order::handle_callback( $order->get_id(), $callback );

		// Update order meta.
		$order->add_meta_data( '_zotapay_callback', time() );
		$order->add_meta_data( '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
		$order->save();

		$order = wc_get_order( $order->get_id() );

		// Check status change
		$this->assertTrue( $handle );
		$this->assertSame( $order->get_status(), 'overpayment' );

		$order_notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );

		// ZotaPay amount.
		$zotapay_amount = \floatval( $order->get_meta( '_zotapay_amount', true ) );

		$this->assertSame( $zotapay_amount, $overpayment );
		$this->assertSame( $order_notes[0]->content, 'Order status changed from Pending payment to Overpayment.' );
	}


	/**
	 * Test all cases with multiple callbacks.
	 *
	 * @dataProvider getDataPartialPayment
	 */
	public function test_amount_cahnged_with_multiple_callbacks( $data ) {
		$original_amount = floatval( $data['data']['extraData']['originalAmount'] );
		$order = $this->create_pending_payment_order( $original_amount );

		$callback_amounts = array( 2.00, 3.00, 5.00, 20.00, 1000.00, $original_amount );

		// Multiple callbacks.
		foreach ( $callback_amounts as $callback_amount ) {
			$order_id = $order->get_id();

			// Change amount.
			$stream = str_replace( '"amount": "5.00"', '"amount": "' . (string) $callback_amount . '"', $data['stream'] );

			// Get the callback handler.
			$callback = new \Zotapay\ApiCallback( $stream );

			// Single callback.
			$handle = \Zota\Zota_WooCommerce\Includes\Order::handle_callback( $order_id, $callback );

			$this->assertTrue( $handle );

			// Update order meta.
			$order->add_meta_data( '_zotapay_callback', time() );
			$order->add_meta_data( '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
			$order->save();

			$order = wc_get_order( $order_id );

			$order_notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );

			// ZotaPay amount.
			$zotapay_amount = \floatval( $order->get_meta( '_zotapay_amount', true ) );

			// Check status change
			if ( $zotapay_amount < $original_amount ) {
				$this->assertSame( 'partial-payment', $order->get_status() );
			} elseif ( $zotapay_amount > $original_amount ) {
				$this->assertSame( 'overpayment', $order->get_status() );
			} else {
				$this->assertSame( $original_amount, $zotapay_amount );
				$this->assertSame( 'processing', $order->get_status() );
			}
		}
	}
}
