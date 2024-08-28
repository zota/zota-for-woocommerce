<?php
/**
 * Zota for WooCommerce
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

use \Zota\Zota_WooCommerce\Includes\Settings;
use \Zota\Zota_WooCommerce\Includes\Order;
use \Zota\Zota_WooCommerce\Includes\Response;
use \Zotapay\Zotapay;
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

	const ZOTAPAY_MAX_PAYMENT_ATTEMPTS = 3; // Max retry for failed payment orders.
	const ZOTAPAY_WAITING_APPROVAL     = 168; // Hours for waiting approval.

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
	 * Redirect url
	 *
	 * @var string
	 */
	public static $redirect_url;

	/**
	 * Callback url
	 *
	 * @var string
	 */
	public static $callback_url;

	/**
	 * Checkout url
	 *
	 * @var string
	 */
	public static $checkout_url;


	/**
	 * Defines main properties, load settings fields and hooks
	 *
	 * @param  string $payment_method Payment method.
	 */
	public function __construct( $payment_method ) {

		// Initial settings.
		$this->id                 = $payment_method;
		$this->has_fields         = false;
		$this->method_title       = $this->get_option( 'title' );
		$this->method_description = $this->get_option( 'description' );
		$this->supports           = array(
			'products',
		);
		$this->version            = ZOTA_WC_VERSION;
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Zota Configuration.
		Settings::init();

		// Hooks.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_api_' . $this->id, array( '\Zota\Zota_WooCommerce\Includes\Response', 'callback' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( '\Zota\Zota_WooCommerce\Includes\Response', 'redirect' ) );
		add_action( 'woocommerce_order_item_add_action_buttons', array( $this, 'order_status_button' ) );
		add_action( 'save_post', array( $this, 'order_status_request' ) );
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
		// Check currency.
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

		// If not is checkout return.
		if ( empty( WC()->customer ) ) {
			return parent::is_available();
		}

		// Check if has routing by countries.
		if ( 'yes' !== $this->get_option( 'routing' ) ) {
			return parent::is_available();
		}

		// Check if has added countries.
		$countries = $this->get_option( 'countries' );
		if ( empty( $countries ) || ! is_array( $countries ) ) {
			return parent::is_available();
		}

		// Is cutomer's billing country in routing countries.
		if ( in_array( WC()->customer->get_billing_country(), $countries, true ) ) {
			return parent::is_available();
		}

		return false;
	}

	/**
	 * Settings Icon Fields.
	 *
	 * @param  string $key Field key.
	 * @param  string $data Field data.
	 * @return string
	 */
	public function generate_icon_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$data['id']    = sprintf( 'woocommerce_%s_icon', $this->id );
		$data['value'] = $this->get_option( 'icon' );

		ob_start();

		Settings::field_icon( $data );

		return ob_get_clean();
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
		global $woocommerce;

		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			wc_add_notice(
				esc_html__( 'Order not found.', 'zota-woocommerce' ),
				'error'
			);
			return;
		}

		// Check if payment attempts are exceeded.
		$payment_attempts = (int) $order->get_meta( '_zotapay_attempts', true );
		if ( $payment_attempts >= self::ZOTAPAY_MAX_PAYMENT_ATTEMPTS ) {
			wc_add_notice(
				'Zota Error: ' . esc_html__( 'Payment attempts exceeded.', 'zota-woocommerce' ),
				'error'
			);
			return;
		}

		// Zota urls.
		self::$redirect_url = $this->get_return_url( $order );
		self::$callback_url = preg_replace( '/^http:/i', 'https:', home_url( '?wc-api=' . $this->id ) );
		self::$checkout_url = $this->get_return_url( $order );

		// Set Zota Endpoint.
		$endpoint = Settings::$testmode ? $this->get_option( 'test_endpoint' ) : $this->get_option( 'endpoint' );
		$endpoint = ! empty( $endpoint ) ? $endpoint : '';
		Zotapay::setEndpoint( $endpoint );

		// Deposit order.
		$deposit_order = Order::deposit_order( $order_id );

		// Deposit request.
		$deposit  = new Deposit();
		$response = $deposit->request( $deposit_order );
		if ( null !== $response->getMessage() ) {
			wc_add_notice(
				'Zota Error: ' . esc_html( '(' . $response->getCode() . ') ' . $response->getMessage() ),
				'error'
			);
			return;
		}

		// Remove cart.
		$woocommerce->cart->empty_cart();

		// Add order meta.
		if ( null !== $response->getMerchantOrderID() ) {
			$order->update_meta_data( '_zotapay_merchant_order_id', sanitize_text_field( $response->getMerchantOrderID() ) );
		}
		if ( null !== $response->getOrderID() ) {
			$order->update_meta_data( '_zotapay_order_id', sanitize_text_field( $response->getOrderID() ) );
		}

		$note = sprintf(
			// translators: %s Zota OrderID.
			esc_html__( 'Zota order created. Zota OrderID: %s.', 'zota-woocommerce' ),
			sanitize_text_field( $response->getOrderID() )
		);
		$order->add_order_note( $note );
		$order->save();

		// Update payment attempts.
		$payment_attempts++;
		$order->update_meta_data( '_zotapay_attempts', $payment_attempts );
		$order->save();

		// Set expiration time.
		Order::set_expiration_time( $order_id );

		// Schedule order status check here, as the user might not get to the thank you page.
		$next_time = time() + 5 * MINUTE_IN_SECONDS;
		if ( class_exists( 'ActionScheduler' ) ) {
			as_unschedule_all_actions( 'zota_scheduled_order_status', array( $order_id ), ZOTA_WC_GATEWAY_ID );
			as_schedule_single_action( $next_time, 'zota_scheduled_order_status', array( $order_id ), ZOTA_WC_GATEWAY_ID );
		} else {
			$next_scheduled = wp_next_scheduled( 'zota_scheduled_order_status', array( $order_id ) );
			if ( false !== $next_scheduled ) {
				wp_unschedule_event( $next_scheduled, 'zota_scheduled_order_status', array( $order_id ) );
			}
			wp_schedule_single_event( $next_time, 'zota_scheduled_order_status', array( $order_id ) );
		}
		$message = sprintf(
			// translators: %s WC Order ID.
			esc_html__( 'Scheduled action added on initial payment request for WC Order #%s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		return array(
			'result'   => 'success',
			'redirect' => $response->getDepositUrl(),
		);
	}


	/**
	 * Get payment method icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		$attachment = null;
		if ( ! empty( $this->get_option( 'icon' ) ) ) {
			$atts       = array(
				'class' => 'zotapay-icon gateway-' . hash( 'crc32b', $this->id ) . '-icon',
			);
			$attachment = wp_get_attachment_image( $this->get_option( 'icon' ), 'medium', false, $atts );
		}

		$icon = apply_filters( 'zota_woocommerce_' . $this->id . '_icon', $attachment );
		if ( empty( $icon ) ) {
			return;
		}

		return $icon;
	}


	/**
	 * Order Status button.
	 */
	public function order_status_button() {
		global $post;

		// Get the order.
		$order = wc_get_order( $post->ID );
		if ( empty( $order ) ) {
			return;
		}

		// Check if payment method is Zota Woocommerce.
		if ( ZOTA_WC_GATEWAY_ID !== $order->get_payment_method() ) {
			return;
		}
		?>
			<button type="submit" name="zota-order-status" class="button zota-order-status" value="1">
				<?php esc_html_e( 'Order Status', 'zota-woocommerce' ); ?>
			</button>
		<?php
	}


	/**
	 * Order Status request.
	 *
	 * @param int $order_id Order ID.
	 */
	public function order_status_request( $order_id ) {

		// Get the order.
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			return;
		}

		// Check if payment method is Zota Woocommerce.
		if ( ZOTA_WC_GATEWAY_ID !== $order->get_payment_method() ) {
			return;
		}

		// Check if is order status request.
		if ( ! isset( $_POST['zota-order-status'] ) || '1' !== $_POST['zota-order-status'] ) { // phpcs:ignore
			return;
		}

		// Order status request.
		$response = Order::order_status( $order_id );

		if ( false === $response ) {
			$order->add_order_note( esc_html__( 'Order Status admin request failed. Maybe order not yet sent to Zota.', 'zota-woocommerce' ) );
			$order->save();
			return;
		}

		$status = ! empty( $response->getStatus() ) ? $response->getStatus() : $response->getErrorMessage();

		$note = sprintf(
			// translators: %1$s Status, %2$s Order ID, %3$s Merchant Order ID.
			esc_html__( 'Order Status request from administration: %1$s. Order ID #%2$s / Merchant Order ID #%3$s', 'zota-woocommerce' ),
			$status,
			$response->getOrderID(),
			$response->getMerchantOrderID()
		);
		$order->add_order_note( $note );
		$order->save();

		// order status update.
		Order::update_status( $order_id, $response );
	}
}
