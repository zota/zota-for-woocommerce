<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zotapay\Zotapay;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class Settings {

	/**
	 * Test mode
	 *
	 * @var bool
	 */
	public static $testmode;

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
				'column_order_id' => array(
					'title' 	  => esc_html__( 'ZotaPay OrderID Column', 'zota-woocommerce' ),
					'label' 	  => esc_html__( 'Display additional column with ZotaPay OrderID in orders list', 'zota-woocommerce' ),
					'type'  	  => 'checkbox',
					'desc_tip'    => true,
					'description' => esc_html__( 'Check this if you want ZotaPay order ID to be shown on orders list page.', 'zota-woocommerce' ),
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
					'desc_tip'    => true,
					'description' => esc_html__( 'Check this to save aditional information during payment process in WooCommerce logs.', 'zota-woocommerce' ),
				)
			)
		);
		// @codingStandardsIgnoreEnd
	}

	/**
	 * WooCommerce settings tab.
	 *
	 * @param array $settings_tabs Settings tabs.
	 *
	 * @return array
	 */
	public static function settings_tab( $settings_tabs ) {
	   $settings_tabs[ZOTA_WC_PLUGIN_ID] = esc_html__( 'ZotaPay', 'zota-woocommerce' );
	   return $settings_tabs;
	}

	/**
	 * Settings tab fields.
	 */
	function settings_tab_fields() {
		// @codingStandardsIgnoreStart
		return apply_filters( ZOTA_WC_PLUGIN_ID . '_settings_tab_fields',
			array(
				array(
	                'name' => esc_html__( 'ZotaPay General Settings', 'zota-woocommerce' ),
	                'type' => 'title',
	                'desc' => esc_html__( 'General settings for connection to ZotaPay', 'zota-woocommerce' ),
	                'id'   => ZOTA_WC_PLUGIN_ID . '_section_general_title'
	            ),
				array(
					'title'   => esc_html__( 'Test Mode', 'zota-woocommerce' ),
					'desc'    => esc_html__( 'Enable test mode', 'zota-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'yes',
					'id'      => ZOTA_WC_PLUGIN_ID . '_testmode'
				),
				array(
					'title'     => esc_html__( 'Test Merchant ID', 'zota-woocommerce' ),
					'type'      => 'text',
					'desc' 		=> esc_html__( 'Merchant ID is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'  => true,
					'class'     => 'test-settings',
					'id'   		=> ZOTA_WC_PLUGIN_ID . '_test_merchant_id'
				),
				array(
					'title'    => esc_html__( 'Test Merchant Secret Key', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc' 	   => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'test-settings',
					'id'   	   => ZOTA_WC_PLUGIN_ID . '_test_merchant_secret_key'
				),
				array(
					'title'       => esc_html__( 'Merchant ID', 'zota-woocommerce' ),
					'type'        => 'text',
					'desc'     => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'live-settings',
					'id'   		  => ZOTA_WC_PLUGIN_ID . '_merchant_id'
				),
				array(
					'title'       => esc_html__( 'Merchant Secret Key', 'zota-woocommerce' ),
					'type'        => 'text',
					'desc' => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
					'class'       => 'live-settings',
					'id'   		  => ZOTA_WC_PLUGIN_ID . '_merchant_secret_key'
				),
				array(
					'title' => esc_html__( 'ZotaPay OrderID Column', 'zota-woocommerce' ),
					'type'  => 'checkbox',
					'desc'  => esc_html__( 'Check this if you want ZotaPay order ID to be shown on orders list page.', 'zota-woocommerce' ),
					'id'   	=> ZOTA_WC_PLUGIN_ID . '_column_order_id'
				),
				array(
					'title' 	  => esc_html__( 'Logging', 'zota-woocommerce' ),
					'desc' => esc_html__( 'Check this to save aditional information during payment process in WooCommerce logs.', 'zota-woocommerce' ),
					'type'  	  => 'checkbox',
					'id'   		  => ZOTA_WC_PLUGIN_ID . '_logging'
				),
	            array(
	                 'type' => 'sectionend',
	                 'id' => ZOTA_WC_PLUGIN_ID . '_section_general_end'
	            )
			)
		);
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Settings tab fields show.
	 */
	public static function settings_tab_fields_show() {
		 woocommerce_admin_fields( self::settings_tab_fields() );
	}

	/**
	 * Settings tab fields update.
	 */
	public static function settings_tab_fields_update() {
		 woocommerce_update_options( self::settings_tab_fields() );
	}

	/**
	 * Init
	 */
	public static function init() {
		$settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );

		self::$testmode = ! empty( $settings['testmode'] ) && 'yes' === $settings['testmode'] ? true : false;

		// API base.
		$api_base            = self::$testmode ? 'https://api.zotapay-sandbox.com' : 'https://api.zotapay.com';

		// Merchant ID.
		$settings_test_merchant_id = ! empty( $settings['test_merchant_id'] ) ? $settings['test_merchant_id'] : '';
		$settings_merchant_id 	   = ! empty( $settings['merchant_id'] ) ? $settings['merchant_id'] : '';
		$merchant_id         	   = self::$testmode ? $settings_test_merchant_id : $settings_merchant_id;

		// Merchant ID.
		$settings_test_merchant_secret_key = ! empty( $settings['test_merchant_secret_key'] ) ? $settings['test_merchant_secret_key'] : '';
		$settings_merchant_secret_key 	   = ! empty( $settings['merchant_secret_key'] ) ? $settings['merchant_secret_key'] : '';
		$merchant_secret_key 			   = self::$testmode ? $settings_test_merchant_secret_key : $settings_merchant_secret_key;

		// ZotaPay settings.
		Zotapay::setApiBase( $api_base );
		Zotapay::setMerchantId( $merchant_id );
		Zotapay::setMerchantSecretKey( $merchant_secret_key );
		Zotapay::setEndpoint( self::endpoint( $settings ) );
		Zotapay::setLogThreshold( self::log_treshold() );
		Zotapay::setLogDestination( self::log_destination() );
	}


	/**
	 * Get Endpoint.
	 *
	 * @param  array $settings Gateway settings.
	 * @return string
	 */
	public static function endpoint( $settings ) {
		if ( ! function_exists( 'get_woocommerce_currency' ) ) {
			return '';
		}

		$endpoint = ( self::$testmode ? 'test_endpoint_' : 'endpoint_' ) . strtolower( get_woocommerce_currency() );
		return isset( $settings[ $endpoint ] ) ? $settings[ $endpoint ] : '';
	}


	/**
	 * Log destination.
	 *
	 * @return string
	 */
	public static function log_destination() {
		// @codingStandardsIgnoreStart
		$date_suffix   = date( 'Y-m-d', time() );
		// @codingStandardsIgnoreEnd
		$handle        = 'zota-woocommerce';
		$hash_suffix   = wp_hash( $handle );
		$log_file_name = sanitize_file_name( implode( '-', array( $handle, $date_suffix, $hash_suffix ) ) . '.log' );

		// Logging destination to WooCommerce log folder.
		if ( defined( 'WC_LOG_DIR' ) ) {
			$log_dir = WC_LOG_DIR;
		} else {
			$upload_dir = wp_upload_dir( null, false );
			$log_dir    = $upload_dir['basedir'] . '/wc-logs/';
		}

		return apply_filters( 'zota_woocommerce_log_destination', $log_dir . $log_file_name );
	}


	/**
	 * Log treshold
	 *
	 * @return string
	 */
	public static function log_treshold() {
		return apply_filters( 'zota_woocommerce_log_treshold', 'info' );
	}


	/**
	 * Scheduled check for pending payment orders
	 *
	 * @return void
	 */
	public static function deactivation() {

		// Zotapay Configuration.
		self::init();

		// Logging treshold.
		self::log_treshold();

		Zotapay::getLogger()->info( esc_html__( 'Deactivation started.', 'zota-woocommerce' ) );

		// Get orders.
		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_order',
			'post_status'    => 'wc-pending',
			'meta_key'       => '_zotapay_expiration', // phpcs:ignore
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			'fields'         => 'ids',
		);
		$orders = get_posts( $args );

		// No pending orders?
		if ( empty( $orders ) ) {
			return;
		}

		// Loop orders.
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );

			if ( empty( $order ) ) {
				continue;
			}

			// Order status.
			$response = Order::order_status( $order_id );
			if ( false === $response ) {
				$error = sprintf(
					// translators: %s WC Order ID.
					esc_html__( 'Order Status failed for order #%s ', 'zota-woocommerce' ),
					$order_id
				);
				Zotapay::getLogger()->info( $error );
				continue;
			}
			if ( null !== $response->getMessage() ) {
				$error = sprintf(
					// translators: %1$s WC Order ID, %2$s Error message.
					esc_html__( 'Order Status failed for order #%1$s. Error: %2$s', 'zota-woocommerce' ),
					$order_id,
					$response->getMessage()
				);
				Zotapay::getLogger()->info( $error );
				continue;
			}

			if ( 'APPROVED' !== $response->getStatus() ) {
				Order::delete_expiration_time( $order_id );
				Order::set_expired( $order_id );
				continue;
			}

			// Update status and meta.
			Order::update_status( $order_id, $response );
			$order->update_meta_data( '_zotapay_order_status', time() );
			$order->save();
		}

		Zotapay::getLogger()->info( esc_html__( 'Deactivation finished.', 'zota-woocommerce' ) );
	}
}
