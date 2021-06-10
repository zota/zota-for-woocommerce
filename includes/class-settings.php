<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zotapay\Zotapay;
use WC_Admin_Settings;

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
		return apply_filters(
			ZOTA_WC_GATEWAY_ID . '_form_fields',
			// @codingStandardsIgnoreStart
			array(
				'enabled' 		=> array(
					'title'   => esc_html__( 'Enable/Disable', 'zota-woocommerce' ),
					'label'   => esc_html__( 'Enable Payment Method', 'zota-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'yes',
				),
				'title' 		=> array(
					'title'       => esc_html__( 'Title', 'zota-woocommerce' ),
					'description' => esc_html__( 'This controls the title which the user sees during checkout.', 'zota-woocommerce' ),
					'default'     => esc_html__( 'Credit Card (Zota)', 'zota-woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
				),
				'description' 	=> array(
					'title'       => esc_html__( 'Description', 'zota-woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => esc_html__( 'This controls the description which the user sees during checkout.', 'zota-woocommerce' ),
					'default'     => esc_html__( 'Pay with your credit card via Zota.', 'zota-woocommerce' ),
				),
				'test_endpoint' => array(
					'title'       => esc_html__( 'Test Endpoint', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Test Endpoint is given (optional) when you create your account at ZotaPay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
				),
				'endpoint' 		=> array(
					'title'       => esc_html__( 'Endpoint', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Endpoint is given (optional) when you create your account at ZotaPay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
				),
				'icon' 		=> array(
					'title'    => esc_html__( 'Logo', 'zota-woocommerce' ),
					'desc' 	   => esc_html__( 'This controls the image which the user sees during checkout.', 'zota-woocommerce' ),
					'type'     => 'icon',
					'default'  => '',
					'desc_tip' => true,
				),
				'routing' 		=> array(
					'title'   => esc_html__( 'Routing by countries', 'zota-woocommerce' ),
					'desc'    => esc_html__( 'Enable payment method routing by countries', 'zota-woocommerce' ),
					'type'    => 'checkbox',
				),
				'countries' 		=> array(
					'title'    => esc_html__( 'Select countries', 'zota-woocommerce' ),
					'desc' 	   => esc_html__( 'Select countries for which this payment method is valid.', 'zota-woocommerce' ),
					'type'     => 'countries',
					'default'  => '',
					'desc_tip' => false,
				),
			)
			// @codingStandardsIgnoreEnd
		);
	}

	/**
	 * WooCommerce settings tab.
	 *
	 * @param array $settings_tabs Settings tabs.
	 *
	 * @return array
	 */
	public static function settings_tab( $settings_tabs ) {
		$settings_tabs['zotapay'] = esc_html__( 'ZotaPay', 'zota-woocommerce' );
		return $settings_tabs;
	}

	/**
	 * Settings tab fields.
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public static function settings_fields( $settings = array() ) {
		return apply_filters(
			ZOTA_WC_PLUGIN_ID . '_settings_fields',
			// @codingStandardsIgnoreStart
			array(
				array(
					'title'   => esc_html__( 'Test Mode', 'zota-woocommerce' ),
					'desc'    => esc_html__( 'Enable test mode', 'zota-woocommerce' ),
					'type'    => 'checkbox',
					'id' 	  => 'zotapay_settings[testmode]',
					'value'   => $settings['testmode']
				),
				array(
					'title'    => esc_html__( 'Test Merchant ID', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc' 	   => esc_html__( 'Merchant ID is given when you create your account at ZotaPay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'test-settings',
					'id' 	   => 'zotapay_settings[test_merchant_id]',
					'value'    => $settings['test_merchant_id']
				),
				array(
					'title'    => esc_html__( 'Test Merchant Secret Key', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc' 	   => esc_html__( 'Merchant Secret Key is given when you create your account at ZotaPay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'test-settings',
					'id' 	   => 'zotapay_settings[test_merchant_secret_key]',
					'value'    => $settings['test_merchant_secret_key']
				),
				array(
					'title'    => esc_html__( 'Merchant ID', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc'     => esc_html__( 'Merchant Secret Key is given when you create your account at ZotaPay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'live-settings',
					'id' 	   => 'zotapay_settings[merchant_id]',
					'value'    => $settings['merchant_id']
				),
				array(
					'title'    => esc_html__( 'Merchant Secret Key', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc'     => esc_html__( 'Merchant Secret Key is given when you create your account at ZotaPay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'live-settings',
					'id' 	   => 'zotapay_settings[merchant_secret_key]',
					'value'    => $settings['merchant_secret_key']
				),
				array(
					'title' => esc_html__( 'ZotaPay OrderID Column', 'zota-woocommerce' ),
					'type'  => 'checkbox',
					'desc'  => esc_html__( 'Check this if you want ZotaPay order ID to be shown on orders list page.', 'zota-woocommerce' ),
					'id' 	   => 'zotapay_settings[column_order_id]',
					'value'    => $settings['column_order_id']
				),
				array(
					'title' 	  => esc_html__( 'Logging', 'zota-woocommerce' ),
					'desc' => esc_html__( 'Check this to save aditional information during payment process in WooCommerce logs.', 'zota-woocommerce' ),
					'type'  	  => 'checkbox',
					'id' 	   => 'zotapay_settings[logging]',
					'value'    => $settings['logging']
				)
			)
			// @codingStandardsIgnoreEnd
		);
	}

	/**
	 * Payment method fields.
	 *
	 * @param string $payment_method_id Payment method id.
	 * @param array  $settings Settings array.
	 */
	public static function payment_method_fields( $payment_method_id, $settings = array() ) {
		if ( empty( $payment_method_id ) ) {
			return;
		}

		$payment_method_fields = array(
			// @codingStandardsIgnoreStart
			array(
				'title'   => esc_html__( 'Enable/Disable', 'zota-woocommerce' ),
				'desc'   => esc_html__( 'Enable Payment Method', 'zota-woocommerce' ),
				'type'    => 'checkbox',
				'id' 	  => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][enabled]',
				'value'   => isset( $settings['enabled'] ) ? $settings['enabled'] : ''
			),
			array(
				'title'    => esc_html__( 'Title', 'zota-woocommerce' ),
				'desc' 	   => esc_html__( 'This controls the title which the user sees during checkout.', 'zota-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => true,
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][title]',
				'value'    => ! empty ( $settings['title'] ) ? $settings['title'] : esc_html__( 'Credit Card (Zota)', 'zota-woocommerce' )
			),
			array(
				'title'    => esc_html__( 'Description', 'zota-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => true,
				'desc' 	   => esc_html__( 'This controls the description which the user sees during checkout.', 'zota-woocommerce' ),
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][description]',
				'value'    => ! empty ( $settings['description'] ) ? $settings['description'] : esc_html__( 'Pay with your credit card via Zota.', 'zota-woocommerce' )
			),
			array(
				'title'    => esc_html__( 'Test Endpoint', 'zota-woocommerce' ),
				'type'     => 'text',
				'desc' 	   => esc_html__( 'The Endpoints are in your account at ZotaPay.', 'zota-woocommerce' ),
				'desc_tip' => true,
				'class'    => 'test-settings',
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][test_endpoint]',
				'value'    => ! empty ( $settings['test_endpoint'] ) ? $settings['test_endpoint'] : ''
			),
			array(
				'title'    => esc_html__( 'Endpoint', 'zota-woocommerce' ),
				'type'     => 'text',
				'desc' 	   => esc_html__( 'The Endpoints are in your account at ZotaPay.', 'zota-woocommerce' ),
				'desc_tip' => true,
				'class'    => 'live-settings',
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][endpoint]',
				'value'    => ! empty ( $settings['endpoint'] ) ? $settings['endpoint'] : ''
			),
			array(
				'title'    => esc_html__( 'Logo', 'zota-woocommerce' ),
				'desc' 	   => esc_html__( 'This controls the image which the user sees during checkout.', 'zota-woocommerce' ),
				'type'     => 'icon',
				'default'  => '',
				'desc_tip' => true,
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][icon]',
				'value'    => ! empty ( $settings['icon'] ) ? $settings['icon'] : ''
			),
			array(
				'title'   => esc_html__( 'Routing by countries', 'zota-woocommerce' ),
				'desc'   => esc_html__( 'Enable payment method routing by countries', 'zota-woocommerce' ),
				'type'    => 'checkbox',
				'id' 	  => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][routing]',
				'value'   => isset( $settings['routing'] ) ? $settings['routing'] : ''
			),
			array(
				'title'    => esc_html__( 'Select countries', 'zota-woocommerce' ),
				'desc' 	   => esc_html__( 'Select countries for which this payment method is valid.', 'zota-woocommerce' ),
				'type'     => 'countries',
				'default'  => '',
				'desc_tip' => false,
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][countries]',
				'value'    => ! empty ( $settings['countries'] ) ? $settings['countries'] : array()
			),
			array(
				'title'       => '',
				'description' => '',
				'type'        => 'remove_payment_method',
				'default'     => '',
				'desc_tip'    => false,
				'id' 		  => esc_attr( $payment_method_id )
			)
			// @codingStandardsIgnoreEnd
		);

		apply_filters( ZOTA_WC_PLUGIN_ID . '_payment_method_fields', $payment_method_fields );

		echo '<table class="form-table payment_method" id="' . esc_attr( $payment_method_id ) . '">';
		woocommerce_admin_fields( $payment_method_fields );
		echo '<tr><td colspan="2"><hr></td></tr>';
		echo '</table>';
	}

	/**
	 * Settings tab fields show.
	 */
	public static function settings_show() {

		?>
		<h2><?php esc_html_e( 'ZotaPay General Settings', 'zota-woocommerce' ); ?></h2>
		<div id="zotapay-section-settings-general-description">
			<p><?php esc_html_e( 'General settings for connection to ZotaPay', 'zota-woocommerce' ); ?></p>
		</div>
		<div id="zotapay-settings-general">
			<table class="form-table">
			<?php

			$zotapay_settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );
			woocommerce_admin_fields( self::settings_fields( $zotapay_settings ) );

			?>
			</table>
		</div>

		<h2><?php esc_html_e( 'Payment Methods', 'zota-woocommerce' ); ?></h2>
		<div id="zotapay-section-payment-methods-description">
			<p><?php esc_html_e( 'Payment Methods registered for use with ZotaPay', 'zota-woocommerce' ); ?></p>
		</div>
		<div id="zotapay-payment-methods">
		<?php

		$payment_methods = get_option( 'zotapay_payment_methods', array() );
		foreach ( $payment_methods as $payment_method ) {
			$payment_method_settings = get_option( 'woocommerce_' . $payment_method . '_settings', array() );
			self::payment_method_fields( $payment_method, $payment_method_settings );
		}

		?>
		</div>
		<br>
		<button id="add-payment-method" class="button-primary" value="<?php esc_html_e( 'Add Payment Method', 'zota-woocommerce' ); ?>">
			<?php esc_html_e( 'Add Payment Method', 'zota-woocommerce' ); ?>
		</button>
		<?php
	}

	/**
	 * Ajax request for adding payment gateway fields.
	 */
	public static function add_payment_method() {
		$payment_method_id = ZOTA_WC_GATEWAY_ID . '_' . uniqid();
		self::payment_method_fields( $payment_method_id );
		wp_die();
	}

	/**
	 * Add media field for payment method's icon.
	 *
	 * @param array $value Settings field data.
	 */
	public static function field_icon( $value ) {
		require ZOTA_WC_PATH . 'templates/field-icon.php';
	}

	/**
	 * Add countries for payment method's routing.
	 *
	 * @param array $value Settings field data.
	 */
	public static function field_countries( $value ) {
		require ZOTA_WC_PATH . 'templates/field-countries.php';
	}

	/**
	 * Remove payment method.
	 *
	 * @param array $value Settings field data.
	 */
	public static function field_remove_payment_method( $value ) {
		require ZOTA_WC_PATH . 'templates/field-remove-payment-method.php';
	}

	/**
	 * Save settings.
	 */
	public static function save_settings() {

		// Save general settings.
		// @codingStandardsIgnoreStart
		// The following input data are arrays and are checked and sanitized below on line 390 and 410.
		$zotapay_settings 		 = isset( $_POST['zotapay_settings'] ) ? $_POST['zotapay_settings'] : array();
		$zotapay_payment_methods = isset( $_POST['zotapay_payment_methods'] ) ? $_POST['zotapay_payment_methods'] : array();
		// @codingStandardsIgnoreEnd

		if ( empty( $zotapay_settings ) || ! is_array( $zotapay_settings ) ) {
			return;
		}

		if ( empty( $zotapay_payment_methods ) || ! is_array( $zotapay_payment_methods ) ) {
			$zotapay_payment_methods = array();
		}

		$settings = array();
		foreach ( $zotapay_settings as $key => $value ) {
			// Fix checkboxes values.
			$value                                   = in_array( $key, array( 'testmode', 'column_order_id', 'logging' ), true ) ? 'yes' : $value;
			$settings[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}
		update_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', $settings, false );

		// Save payment methods settings.
		$payment_methods = array();
		foreach ( $zotapay_payment_methods as $payment_method_id => $payment_method_settings ) {

			// If marked for removal delete settings.
			if ( isset( $payment_method_settings['remove'] ) ) {
				delete_option( 'woocommerce_' . $payment_method_id . '_settings' );
				continue;
			}

			// Add payment method id for zotapay_payment_methods option.
			$payment_methods[] = $payment_method_id;

			// Update payment method settings.
			foreach ( $payment_method_settings as $key => $value ) {
				if ( 'countries' === $key ) {
					if ( empty( $payment_method_settings['countries'] ) ) {
						constinue;
					}

					$countries = array();
					foreach ( $payment_method_settings['countries'] as $country ) {
						$countries[] = sanitize_text_field( $country );
					}
					$payment_method_settings['countries'] = $countries;
					continue;
				}

				$value = 'enabled' === $key ? 'yes' : $value;
				$payment_method_settings[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
			}
			update_option( 'woocommerce_' . $payment_method_id . '_settings', $payment_method_settings, true );
		}

		// Save all payment methods ids to zotapay_payment_methods option.
		update_option( 'zotapay_payment_methods', $payment_methods, false );
	}

	/**
	 * Init
	 */
	public static function init() {
		$settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );

		self::$testmode = ! empty( $settings['testmode'] ) && 'yes' === $settings['testmode'] ? true : false;

		// API base.
		$api_base = self::$testmode ? 'https://api.zotapay-sandbox.com' : 'https://api.zotapay.com';

		// Merchant ID.
		$settings_test_merchant_id = ! empty( $settings['test_merchant_id'] ) ? $settings['test_merchant_id'] : '';
		$settings_merchant_id      = ! empty( $settings['merchant_id'] ) ? $settings['merchant_id'] : '';
		$merchant_id               = self::$testmode ? $settings_test_merchant_id : $settings_merchant_id;

		// Merchant ID.
		$settings_test_merchant_secret_key = ! empty( $settings['test_merchant_secret_key'] ) ? $settings['test_merchant_secret_key'] : '';
		$settings_merchant_secret_key      = ! empty( $settings['merchant_secret_key'] ) ? $settings['merchant_secret_key'] : '';
		$merchant_secret_key               = self::$testmode ? $settings_test_merchant_secret_key : $settings_merchant_secret_key;

		// ZotaPay settings.
		Zotapay::setApiBase( $api_base );
		Zotapay::setMerchantId( $merchant_id );
		Zotapay::setMerchantSecretKey( $merchant_secret_key );
		Zotapay::setLogDestination( self::log_destination() );

		// Logging treshold.
		if ( 'yes' === $settings['logging'] ) {
			Zotapay::setLogThreshold( self::log_treshold() );
		}
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

		// ZotaPay Configuration.
		self::init();

		// Logging treshold.
		self::log_treshold();

		Zotapay::getLogger()->info( esc_html__( 'Deactivation started.', 'zota-woocommerce' ) );

		// Get orders.
		$args   = array(
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

			// Remove scheduled actions.
			if ( class_exists( 'ActionScheduler' ) ) {
				as_unschedule_all_actions( 'zota_scheduled_order_status', array( $order_id ), ZOTA_WC_GATEWAY_ID );
			} else {
				$next_scheduled = wp_next_scheduled( 'zota_scheduled_order_status', array( $order_id ) );
				if ( false !== $next_scheduled ) {
					wp_unschedule_event( $next_scheduled, 'zota_scheduled_order_status', array( $order_id ) );
				}
			}
		}

		Zotapay::getLogger()->info( esc_html__( 'Deactivation finished.', 'zota-woocommerce' ) );
	}
}
