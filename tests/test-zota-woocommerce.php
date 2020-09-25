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
		WC()->session = null;
		$wc_payment_gateways = WC_Payment_Gateways::instance();
		$wc_payment_gateways->init();
		var_dump( array_keys( $wc_payment_gateways->payment_gateways() ) );
		foreach ( $wc_payment_gateways->payment_gateways() as $name => $gateway ) {
			if ( in_array( $name, array( ZOTA_WC_GATEWAY_ID ) ) ) {
				$gateway->enabled = 'yes';
			}
		}
	}

	/**
	 * Initialize session that some tests might have removed.
	 */
	public function tearDown() {
		parent::tearDown();
		WC()->initialize_session();
	}

	/**
	 * Test that Zota payment gateway is enabled.
	 */
	public function test_gateway_enabled() {
		WC()->initialize_session();
		wp_set_current_user( 1 );

		$gateways = WC()->payment_gateways()->get_available_payment_gateways();
		var_dump( $gateways );
		$this->assertFalse( empty( $gateways[ ZOTA_WC_GATEWAY_ID ] ) );
	}
}
