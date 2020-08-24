<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class Settings {

	/**
	 * Admin
	 */
	public static function form_fields() {
		$woocommerce_currency = get_woocommerce_currency();

		// @codingStandardsIgnoreStart
		return apply_filters(
			ZOTA_WC_GATEWAY_ID . '_form_fields',
			array(
				'enabled' => array(
					'title'   => esc_html__( 'Enable/Disable', 'zota-woocommerce' ),
					'label'   => esc_html__( 'Enable Zota', 'zota-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'yes',
				),
				'title' => array(
					'title'       => esc_html__( 'Title', 'zota-woocommerce' ),
					'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'zota-woocommerce' ),
					'default'     => esc_html__( 'Credit Card (Zota)', 'zota-woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => esc_html__( 'Description', 'zota-woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => esc_html__( 'This controls the description which the user sees during checkout.', 'zota-woocommerce' ),
					'default'     => esc_html__( 'Pay with your credit card via Zota.', 'zota-woocommerce' ),
				),
				'testmode' => array(
					'title'   => esc_html__( 'Test Mode', 'zota-woocommerce' ),
					'label'   => esc_html__( 'Enable test mode', 'zota-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'yes',
				),
				'test_merchant_id' => array(
					'title'       => esc_html__( 'Test Merchant ID', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Merchant ID is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'test-settings',
				),
				'test_merchant_secret_key' => array(
					'title'       => esc_html__( 'Test Merchant Secret Key', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'test-settings',
				),
				'test_endpoint_' . strtolower( get_woocommerce_currency() ) => array(
					'title'       => esc_html__( 'Test Endpoint ' . get_woocommerce_currency(), 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Test Endpoint ' . get_woocommerce_currency() . ' is given (optional) when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'test-settings',
				),
				'merchant_id' => array(
					'title'       => esc_html__( 'Merchant ID', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'live-settings',
				),
				'merchant_secret_key' => array(
					'title'       => esc_html__( 'Merchant Secret Key', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'live-settings',
				),
				'endpoint_' . strtolower( get_woocommerce_currency() ) => array(
					'title'       => esc_html__( 'Endpoint ' . get_woocommerce_currency(), 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Endpoint ' . get_woocommerce_currency() . ' is given (optional) when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'live-settings',
				),
				'logging' => array(
					'title' => esc_html__( 'Logging', 'zota-woocommerce' ),
					'label' => esc_html__( 'Enable logging', 'zota-woocommerce' ),
					'type'  => 'checkbox',
				)
			)
		);
		// @codingStandardsIgnoreEnd
	}


	/**
	 * Remove the test prefix from Order ID.
	 *
	 * @param  int $order_id Order ID.
	 * @return int
	 */
	public static function remove_test_prefix( $order_id ) {
		if ( preg_match( '/(.*)-test-(.*)/', $order_id, $matches ) === 1 ) {
			if ( ! empty( $matches[2] ) ) {
				return (int) $matches[2];
			}
		}
		return $order_id;
	}
}
