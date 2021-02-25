<?php
/**
 * @package Zota_Woocommerce
 */

/**
 * Class WC_Tests_Payment_Gateway.
 */
class WC_Tests_Zota_WooCommerce extends WC_Unit_Test_Case {

	private $payment_method;

	/**
	 * Setup, enable payment gateways Cash on delivery and direct bank deposit.
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
	 * Test that Zota payment gateway constructor adds ation hooks successfully.
	 */
	public function test_action_hooks() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->payment_gateways();

		$this->assertNotEmpty( $gateways[ $this->payment_method ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ $this->payment_method ] );

		$zota = $gateways[ $this->payment_method ];

		$this->assertSame( has_action( 'admin_enqueue_scripts', [ $zota, 'admin_enqueue_scripts' ] ), 10 );

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

		\Zotapay\Zotapay::setMockResponse( $mockResponse );

		$result = $payment_gateways[ $order->get_payment_method() ]->process_payment( $order->get_id() );

		$this->assertSame( $result['result'], 'success' );
		$this->assertSame( $result['redirect'], 'https://example.com' );
		$this->assertSame( $order->get_meta( '_zotapay_order_id' ), '1234' );
		$this->assertSame( intval( $order->get_meta( '_zotapay_merchant_order_id' ) ), $order->get_id() );
	}

}
