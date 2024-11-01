<?php
/**
 * @package Zota_Woocommerce
 */

/**
 * Class WC_Tests_Payment_Gateway.
 */
class WC_Tests_Zota_WooCommerce extends WP_UnitTestCase {

	private $payment_method;

	/**
	 * Setup, enable payment gateways Cash on delivery and direct bank deposit.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->payment_method = ZOTA_WC_GATEWAY_ID . '_' . uniqid();
		Tests_Helper::setUp( $this->payment_method );
	}

	/**
	 * Initialize session that some tests might have removed.
	 */
	public function tearDown(): void {
		parent::tearDown();
		WC()->initialize_session();
	}

	/**
	 * Test that Zota payment gateway constructor adds ation hooks successfully.
	 */
	public function test_action_hooks() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->payment_gateways();

		$this->assertNotEmpty( $gateways[ $this->payment_method ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ $this->payment_method ] );

		$zota = $gateways[ $this->payment_method ];
		
		$this->assertSame(
			has_action(
				'woocommerce_update_options_payment_gateways_' . $zota->id,
				[ $zota, 'process_admin_options' ]
			),
			10
		);

		$this->assertSame(
			has_action(
				'woocommerce_api_' . $zota->id,
				[ '\Zota\Zota_WooCommerce\Includes\Response', 'callback' ]
			),
			10
		);

		$this->assertSame(
			has_action(
				'woocommerce_thankyou_' . $zota->id,
				[ '\Zota\Zota_WooCommerce\Includes\Response', 'redirect' ]
			),
			10
		);

		$this->assertSame(
			has_action(
				'woocommerce_order_item_add_action_buttons',
				[ $zota, 'order_status_button' ]
			),
			10
		);

		$this->assertSame( has_action( 'save_post', [ $zota, 'order_status_request' ] ), 10 );
	}

	/**
	 * Test that Zota payment gateway is suppoted for the shop currency (USD).
	 */
	public function test_gateway_supported() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->payment_gateways();

		$this->assertNotEmpty( $gateways[ $this->payment_method ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ $this->payment_method ] );
		$this->assertTrue( $gateways[ $this->payment_method ]->is_supported() );
	}

	/**
	 * Test that Zota payment gateway is available.
	 */
	public function test_gateway_available() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->payment_gateways();

		$this->assertNotEmpty( $gateways[ $this->payment_method ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ $this->payment_method ] );
		$this->assertTrue( $gateways[ $this->payment_method ]->is_available() );
	}

	/**
	 * Test that Zota payment gateway is enabled.
	 */
	public function test_gateway_enabled() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->get_available_payment_gateways();

		$this->assertNotEmpty( $gateways[ $this->payment_method ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ $this->payment_method ] );
	}

	/**
	 * Test payment processing.
	 */
	public function test_process_mayment() {
		WC()->initialize_session();

		$payment_gateways = WC()->payment_gateways->payment_gateways();

		$product = WC_Helper_Product::create_simple_product(
			true,
			[
				'tax_status' => 'none',
				'virtual' => true,
			]
		);

		WC()->cart->add_to_cart( $product->get_id() );

		$order = WC_Helper_Order::create_order( true, $product, $this->payment_method );

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

		\Zota\Zota::setMockResponse( $mockResponse );

		$zota = $payment_gateways[ $order->get_payment_method() ];

		$result = $zota->process_payment( $order->get_id() );

		$this->assertSame( $result['result'], 'success' );
		$this->assertSame( $result['redirect'], 'https://example.com' );
		$this->assertSame( $order->get_meta( '_zotapay_order_id' ), '1234' );
		$this->assertSame( intval( $order->get_meta( '_zotapay_merchant_order_id' ) ), $order->get_id() );

		/**
		 * Set $_GET here because the redirect request handler deals
		 * directly with it.
		 */
		$_GET = [
			'billingDescriptor' => '',
			'merchantOrderID' => $order->get_id(),
			'orderID' => '1234',
			'status' => 'APPROVED',
		];

		$verify['status'] = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$verify['orderID'] = isset( $_GET['orderID'] ) ? $_GET['orderID'] : '';
		$verify['merchantOrderID'] = isset( $_GET['merchantOrderID'] ) ? $_GET['merchantOrderID'] : '';
		$verify['merchantSecretKey'] = \Zota\Zota::getMerchantSecretKey();

		$_GET['signature'] = hash( 'sha256', \implode( '', $verify ) );

		// Trigger redirect request to thank you page.
		do_action( 'woocommerce_thankyou_' . $zota->id, $order->get_id() );

		$order = wc_get_order( $order->get_id() );

		$order_status = apply_filters( 'woocommerce_payment_complete_order_status', $order->needs_processing() ? 'processing' : 'completed', $order->get_id(), $order );

		$this->assertSame( $order->get_meta( '_zotapay_status', true ), $_GET['status'] );
		$this->assertSame( $order->get_status(), $order_status );
		$this->assertTrue( $order->is_paid() );
	}

}
