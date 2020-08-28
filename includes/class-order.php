<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zotapay\DepositOrder;
use \Zotapay\OrderStatusData;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order class.
 */
class Order {

	/**
	 * WooCommerce Gateway object.
	 *
	 * @var Zota_WooCommerce
	 */
	private $gateway;

	/**
	 * Set the gateway property.
	 *
	 * @param Zota_WooCommerce $gateway WooCommerce Gateway.
	 */
	public function __construct( $gateway ) {
		$this->gateway = $gateway;
	}

	/**
	 * Zotapay deposit request.
	 *
	 * @param  int $order_id Order ID.
	 * @return DepositOrder
	 */
	public function deposit_order( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		// Prepare deposit order.
		$deposit_order = new DepositOrder();
		$deposit_order->setMerchantOrderID( $this->gateway->test_prefix . $order->get_id() );
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
		$deposit_order->setCustomerIP( \WC_Geolocation::get_ip_address() );
		$deposit_order->setCustomerBankCode( '' );
		$deposit_order->setRedirectUrl( $this->gateway->get_return_url( $order ) );
		$deposit_order->setCallbackUrl( $this->gateway->callback_url );
		$deposit_order->setCheckoutUrl( $this->gateway->get_return_url( $order ) );
		$deposit_order->setLanguage( 'EN' );

		return $deposit_order;
	}


	/**
	 * Zotapay deposit request.
	 *
	 * @param  int $order_id Order ID.
	 * @return string|false
	 */
	public function order_status_data( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		// Prepare deposit order.
		$deposit_order = new DepositOrder();
		$deposit_order->setMerchantOrderID( $this->gateway->test_prefix . $order->get_id() );
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
		$deposit_order->setCustomerIP( \WC_Geolocation::get_ip_address() );
		$deposit_order->setCustomerBankCode( '' );
		$deposit_order->setRedirectUrl( $this->gateway->get_return_url( $order ) );
		$deposit_order->setCallbackUrl( $this->gateway->callback_url );
		$deposit_order->setCheckoutUrl( $this->gateway->get_return_url( $order ) );
		$deposit_order->setLanguage( 'EN' );

		return $deposit_order;
	}
}
