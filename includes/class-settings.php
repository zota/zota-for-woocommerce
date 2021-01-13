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
					'description' => esc_html__( 'Test Endpoint is given (optional) when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
				),
				'endpoint' 		=> array(
					'title'       => esc_html__( 'Endpoint', 'zota-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Endpoint is given (optional) when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip'    => true,
				),
				'icon' 		=> array(
					'title'    => esc_html__( 'Logo', 'zota-woocommerce' ),
					'desc' 	   => esc_html__( 'This controls the image which the user sees during checkout.', 'zota-woocommerce' ),
					'type'     => 'icon',
					'default'  => '',
					'desc_tip' => true,
				)
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
	 * @param array $settings Settings array
	 *
	 * @return array
	 */
	public static function settings_fields( $settings = array() ) {

		return apply_filters( ZOTA_WC_PLUGIN_ID . '_settings_fields',
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
					'desc' 	   => esc_html__( 'Merchant ID is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'test-settings',
					'id' 	   => 'zotapay_settings[test_merchant_id]',
					'value'    => $settings['test_merchant_id']
				),
				array(
					'title'    => esc_html__( 'Test Merchant Secret Key', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc' 	   => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'test-settings',
					'id' 	   => 'zotapay_settings[test_merchant_secret_key]',
					'value'    => $settings['test_merchant_secret_key']
				),
				array(
					'title'    => esc_html__( 'Merchant ID', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc'     => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
					'desc_tip' => true,
					'class'    => 'live-settings',
					'id' 	   => 'zotapay_settings[merchant_id]',
					'value'    => $settings['merchant_id']
				),
				array(
					'title'    => esc_html__( 'Merchant Secret Key', 'zota-woocommerce' ),
					'type'     => 'text',
					'desc'     => esc_html__( 'Merchant Secret Key is given when you create your account at Zotapay.', 'zota-woocommerce' ),
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
	 * @param array $settings Settings array
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
				'value'   => $settings['enabled']
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
				'desc' 	   => esc_html__( 'The Endpoints are in your account at Zotapay.', 'zota-woocommerce' ),
				'desc_tip' => true,
				'class'    => 'test-settings',
				'id' 	   => 'zotapay_payment_methods[' . esc_attr( $payment_method_id ) . '][test_endpoint]',
				'value'    => ! empty ( $settings['test_endpoint'] ) ? $settings['test_endpoint'] : ''
			),
			array(
				'title'    => esc_html__( 'Endpoint', 'zota-woocommerce' ),
				'type'     => 'text',
				'desc' 	   => esc_html__( 'The Endpoints are in your account at Zotapay.', 'zota-woocommerce' ),
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

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>">
					<?php echo esc_html( $value['title'] ); ?>
					<span class="woocommerce-help-tip" data-tip="<?php echo esc_html( $value['desc'] ); ?>"></span>
				</label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( $value['type'] ) ?>">
				<input
					type="hidden"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					name="<?php echo esc_attr( $value['id'] ); ?>"
					value="<?php echo esc_attr( $value['value'] ); ?>"
					>
				<img
					src="<?php echo ! empty( $value['value'] ) ? esc_url( wp_get_attachment_image_url( $value['value'], 'medium' ) ) : ''; ?>"
					width="300"
					style="display:<?php echo ! empty( $value['value'] ) ? 'block' : 'none'; ?>"
					>
				<p class="controls">
					<button class="button-primary add-media">
						<?php esc_html_e( 'Add Logo', 'zota-woocommerce' ); ?>
					</button>
					<button class="button-secondary remove-media" style="display:<?php echo ! empty( $value['value'] ) ? 'inline-block' : 'none'; ?>">
						<?php esc_html_e( 'Remove Logo', 'zota-woocommerce' ); ?>
					</button>
				</p>
			</td>
		</tr>
	    <?php
	}

	/**
	 * Remove payment method.
	 *
	 * @param array $value Settings field data.
	 */
	public static function field_remove_payment_method( $value ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
			</th>
			<td class="forminp forminp-<?php echo esc_attr( $value['type'] ) ?>">
				<button
					id="remove-payment-method-<?php echo esc_attr( $value['id'] ); ?>"
					class="button remove-payment-method"
					data-id="<?php echo esc_attr( $value['id'] ); ?>"
					value="<?php esc_html_e( 'Remove Payment Method', 'zota-woocommerce' ); ?>"
					>
					<?php esc_html_e( 'Remove Payment Method', 'zota-woocommerce' ); ?>
				</button>
			</td>
		</tr>
	    <?php
	}

	/**
	 * Save settings.
	 */
	public static function save_settings() {

		// Save general settings.
		if ( empty( $_POST['zotapay_settings'] ) ) {
			return;
		}

		$settings = array();
		foreach ( $_POST['zotapay_settings'] as $key => $value ) {
			// Fix checkboxes values.
			$value = in_array( $key,  array( 'testmode', 'column_order_id', 'logging' ) ) ? 'yes' : $value;
			$settings[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}
		update_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', $settings, false );

		// Save payment methods settings.
		$payment_methods = array();
		foreach ( $_POST['zotapay_payment_methods'] as $payment_method_id => $payment_method_settings ) {

			// If marked for removal delete settings.
			if ( isset( $payment_method_settings['remove'] ) ) {
				delete_option( 'woocommerce_' . $payment_method_id . '_settings' );
				continue;
			}

			// Add payment method id for zotapay_payment_methods option.
			$payment_methods[] = $payment_method_id;

			// Update payment method settings.
			foreach ( $payment_method_settings as $key => $value ) {
				$value = $key === 'enabled' ? 'yes' : $value;
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
		Zotapay::setLogDestination( self::log_destination() );

		// Logging treshold.
		if ( 'yes' === $settings['logging'] ) {
			self::log_treshold();
		}
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
