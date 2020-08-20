<?php
/**
 * Zota for WooCommerce
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

use \Zota\Zota_WooCommerce\Includes\Settings;
use \Zotapay\Zotapay;
use \Zotapay\DepositOrder;
use \Zotapay\Deposit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zota_WooCommerce class.
 *
 * @extends WC_Payment_Gateway
 */
class Zota_WooCommerce extends WC_Payment_Gateway {

	/**
	 * Zota Supported currencies
	 *
	 * @var array
	 */
	public static $supported_currencies = array(
		'USD',
		'EUR',
		'MYR',
		'VND',
		'THB',
		'IDR',
		'CNY',
	);

	/**
	 * The url for notifications
	 *
	 * @var string
	 */
	public $url_notify;

	/**
	 * The test prefix
	 *
	 * @var string
	 */
	public $test_prefix;

	/**
	 * Defines main properties, load settings fields and hooks
	 */
	public function __construct() {

		// Initial settings.
		$this->id                 = ZOTA_WC_GATEWAY_ID;
		$this->icon               = ZOTA_WC_URL . 'dist/img/logo.png';
		$this->has_fields         = false;
		$this->method_title       = ZOTA_WC_NAME;
		$this->method_description = esc_html__( 'Add card payments to WooCommerce with Zota', 'zota-woocommerce' );
		$this->supports           = array(
			'products',
		);
		$this->version            = ZOTA_WC_VERSION;
		$this->url_notify         = preg_replace( '/^http:/i', 'https:', home_url( '?wc-api=' . $this->id ) );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Test prefix.
		$testmode = false === empty( $this->get_option( 'testmode' ) ) ? true : false;
		if ( empty( $this->get_option( 'test_prefix' ) ) ) {
			$this->update_option( 'test_prefix', hash( 'crc32', get_bloginfo( 'url' ) ) . '-test-' );
		}
		$this->test_prefix = $testmode ? $this->get_option( 'test_prefix' ) : '';

		// Texts.
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );

		// Zotapay Configuration.
		$merchant_id         = $this->get_option( $testmode ? 'test_merchant_id' : 'merchant_id' );
		$merchant_secret_key = $testmode ? $this->get_option( 'test_merchant_secret_key' ) : $this->get_option( 'merchant_secret_key' );
		$endpoint            = $testmode ? 'test_endpoint_' : 'endpoint_';
		$api_base            = $testmode ? 'https://api.zotapay-sandbox.com' : 'https://api.zotapay.com';

		Zotapay::setMerchantId( $this->get_option( $testmode ? 'test_merchant_id' : 'merchant_id' ) );
		Zotapay::setMerchantSecretKey( $this->get_option( $testmode ? 'test_merchant_secret_key' : 'merchant_secret_key' ) );
		Zotapay::setEndpoint( $this->get_option( ( $testmode ? 'test_endpoint_' : 'endpoint_' ) . strtolower( get_woocommerce_currency() ) ) );
		Zotapay::setApiBase( $testmode ? 'https://api.zotapay-sandbox.com' : 'https://api.zotapay.com' );

		// Hooks.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		if ( $this->is_available() ) {
			add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt' ) );
		}
	}

	/**
	 * Get supported currencies
	 *
	 * @return array
	 */
	public function supported_currencies() {
		return apply_filters( ZOTA_WC_GATEWAY_ID . '_supported_currencies', self::$supported_currencies );
	}

	/**
	 * Check if the currency is in the supported currencies
	 *
	 * @return bool
	 */
	public function is_supported() {
		return in_array( get_woocommerce_currency(), $this->supported_currencies(), true );
	}

	/**
	 * Check if the gateway is available.
	 *
	 * @return false|self
	 */
	public function is_available() {
		if ( ! $this->is_supported() ) {
			return false;
		}

		return parent::is_available();
	}

	/**
	 * Settings Form Fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		if ( false === $this->is_supported() ) {
			return;
		}

		$this->form_fields = Settings::form_fields();
	}

	/**
	 * Admin options scripts.
	 *
	 * @param  string $hook WooCommerce Hook.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( 'woocommerce_page_wc-settings' !== $hook ) {
			return;
		}
		wp_enqueue_script( 'zota-woocommerce', ZOTA_WC_URL . '/dist/js/admin.js', array(), ZOTA_WC_VERSION, true );
	}

	/**
	 * Admin Panel Options
	 *
	 * @return string|self
	 */
	public function admin_options() {
		if ( false === $this->is_supported() ) {
			?>
			<div class="inline error">
				<p>
					<strong><?php esc_html_e( 'Gateway Disabled', 'zota-woocommerce' ); ?></strong>:
					<?php esc_html_e( 'Zota does not support your store currency.', 'zota-woocommerce' ); ?>
				</p>
			</div>
			<?php
			return;
		}

		parent::admin_options();
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Prepare deposit order.
		$deposit_order = new DepositOrder();
		$deposit_order->setMerchantOrderID( $this->test_prefix . $order->get_id() );
		$deposit_order->setMerchantOrderDesc( ZOTA_WC_NAME . '. Order Number: ' . $order->get_order_number() );
		$deposit_order->setOrderAmount( number_format( $order->get_total(), 2, '.', '' ) );
		$deposit_order->setOrderCurrency( $order->get_currency() );
		$deposit_order->setCustomerEmail( $order->get_billing_email() );
		$deposit_order->setCustomerFirstName( $order->get_billing_first_name() );
		$deposit_order->setCustomerLastName( $order->get_billing_last_name() );
		$deposit_order->setCustomerAddress( $order->get_billing_address_1() );
		$deposit_order->setCustomerCountryCode( $order->get_billing_country() );
		$deposit_order->setCustomerCity( $order->get_billing_city() );
		$deposit_order->setCustomerState( '' );
		$deposit_order->setCustomerZipCode( $order->get_billing_postcode() );
		$deposit_order->setCustomerPhone( $order->get_billing_phone() );
		$deposit_order->setCustomerIP( WC_Geolocation::get_ip_address() );
		$deposit_order->setCustomerBankCode( '' );
		$deposit_order->setRedirectUrl( $this->url_notify );
		$deposit_order->setCallbackUrl( $this->url_notify );
		$deposit_order->setCheckoutUrl( $this->url_notify );
		$deposit_order->setLanguage( 'EN' );

		// Deposit request.
		$deposit  = new Deposit();
		$response = $deposit->request( $deposit_order );

		if ( null !== $response->getMessage() ) {
			wc_add_notice(
				'Zotapay Error: ' . esc_html( '(' . $response->getCode() . ') ' . $response->getMessage() ),
				'error'
			);
			return;
		}

		// https://wordpress.local/?wc-api=wc_gateway_zota&billingDescriptor=sandbox-payment&merchantOrderID=71222732-test-1917&orderID=24054137&signature=32b4c5162d37ead656009ba1cab95f35afaf72625735106c0dc502088e2d0c5b&status=APPROVED

		return array(
			'result'   => 'success',
			'redirect' => $response->getDepositUrl(),
		);
	}
}
