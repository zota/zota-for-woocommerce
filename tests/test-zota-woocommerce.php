<?php
/**
 * @package Zota_Woocommerce
 */

/**
 * Class WC_Tests_Payment_Gateway.
 */
class WC_Tests_Zota_WooCommerce extends WC_Unit_Test_Case {

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
			'test_endpoint_' . strtolower( get_woocommerce_currency() ) => 'dummy_endpoint',
			'enabled' => 'yes',
		];
		update_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', $settings );

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

		$this->assertNotEmpty( $gateways[ ZOTA_WC_GATEWAY_ID ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ ZOTA_WC_GATEWAY_ID ] );

		$zota = $gateways[ ZOTA_WC_GATEWAY_ID ];

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

		$this->assertNotEmpty( $gateways[ ZOTA_WC_GATEWAY_ID ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ ZOTA_WC_GATEWAY_ID ] );
		$this->assertTrue( $gateways[ ZOTA_WC_GATEWAY_ID ]->is_supported() );
	}

	/**
	 * Test that Zota payment gateway is available.
	 */
	public function test_gateway_available() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->payment_gateways();

		$this->assertNotEmpty( $gateways[ ZOTA_WC_GATEWAY_ID ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ ZOTA_WC_GATEWAY_ID ] );
		$this->assertTrue( $gateways[ ZOTA_WC_GATEWAY_ID ]->is_available() );
	}

	/**
	 * Test that Zota payment gateway is enabled.
	 */
	public function test_gateway_enabled() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->get_available_payment_gateways();

		$this->assertNotEmpty( $gateways[ ZOTA_WC_GATEWAY_ID ] );
		$this->assertInstanceOf( 'Zota_WooCommerce', $gateways[ ZOTA_WC_GATEWAY_ID ] );
	}

}
