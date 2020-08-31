<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zotapay\Zotapay;
use \Zotapay\DepositOrder;
use \Zotapay\OrderStatus;
use \Zotapay\OrderStatusData;
use \Zotapay\Exception\InvalidSignatureException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order class.
 */
class Order {

	/**
	 * Prepare deposit request data.
	 *
	 * @param  int $order_id Order ID.
	 * @return \Zotapay\DepositOrder|false
	 */
	public static function deposit_order( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Deposit order WC Order #%1$s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		$deposit_order = new DepositOrder();

		$deposit_order->setMerchantOrderID( \Zota_WooCommerce::$test_prefix . $order->get_id() );
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
		$deposit_order->setRedirectUrl( \Zota_WooCommerce::$redirect_url );
		$deposit_order->setCallbackUrl( \Zota_WooCommerce::$callback_url );
		$deposit_order->setCheckoutUrl( \Zota_WooCommerce::$checkout_url );
		$deposit_order->setLanguage( 'EN' );

		return $deposit_order;
	}


	/**
	 * Prepare order status data.
	 *
	 * @param  int $order_id Order ID.
	 * @return \Zotapay\OrderStatusData|false
	 */
	public static function order_status_data( $order_id ) {
		$order = wc_get_order( $order_id );

		// Get WC Order.
		if ( ! $order ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data WC Order #%1$s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Get Zotapay OrderID.
		$zotapay_order_id = get_post_meta( $order_id, '_zotapay_order_id', true );
		if ( true === empty( $zotapay_order_id ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data preparation Zotapay OrderID (order meta) not found for WC Order #%1$s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		$order_status_data = new OrderStatusData();

		$order_status_data->setOrderID( $zotapay_order_id );
		$order_status_data->setMerchantOrderID( \Zota_WooCommerce::$test_prefix . $order->get_id() );

		return $order_status_data;
	}


	/**
	 * Order status request.
	 *
	 * @param  int $order_id Order ID.
	 * @return bool
	 */
	public static function order_status( $order_id ) {
		$order_status_data = self::order_status_data( $order_id );
		if ( false === $order_status_data ) {
			return false;
		}

		$order_status = new OrderStatus();

		return $order_status->request( $order_status_data );
	}


	/**
	 * Process order status response.
	 *
	 * @param  int                  $order_id  Order ID.
	 * @param  \Zotapay\ApiResponse $response Response Status.
	 * @return void
	 */
	public static function update_status( $order_id, $response ) {

		// Get the order.
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Update status WC Order #%1$s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return;
		}

		// If no change do nothing.
		if ( get_post_meta( $order->get_id(), '_zotapay_status', true ) === $response->getStatus() ) {
			return;
		}

		// Status PENDING.
		if ( 'PENDING' === $response->getStatus() ) {
			return;
		}

		// Status APPROVED.
		if ( 'APPROVED' === $response->getStatus() ) {

			if ( false === empty( $response->getProcessorTransactionID() ) ) {
				$note = sprintf(
					// translators: %1$s Processor Transaction ID, %2$s OrderID.
					esc_html__( 'Zotapay Processor Transaction ID: %1$s, OrderID: %2$s.', 'zota-woocommerce' ),
					sanitize_text_field( $response->getProcessorTransactionID() ),
					sanitize_text_field( $response->getOrderID() )
				);
				$order->add_order_note( $note );
				$order->save();
			}

			// If order is paid do nothing.
			if ( $order->is_paid() ) {
				return;
			}

			$order->payment_complete();
			return;
		}

		// Add order note with the status and error message.
		if ( false === empty( $response->getProcessorTransactionID() ) ) {
			$note = sprintf(
				// translators: %1$s Processor Transaction ID, %2$s OrderID, %3$s Status, %4$s Error message.
				esc_html__( 'Zotapay Processor Transaction ID: %1$s, OrderID: %2$s, Status: %3$s, Error: %4$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response->getProcessorTransactionID() ),
				sanitize_text_field( $response->getOrderID() ),
				sanitize_text_field( $response->getStatus() ),
				sanitize_text_field( $response->getErrorMessage() )
			);
		} else {
			$note = sprintf(
				// translators: %1$s OrderID, %2$s Status, %3$s Error message.
				esc_html__( 'Zotapay OrderID: %1$s, Status: %2$s, Error: %3$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response->getOrderID() ),
				sanitize_text_field( $response->getStatus() ),
				sanitize_text_field( $response->getErrorMessage() )
			);
		}
		$order->update_status( 'failed', $note );

		// Update order meta.
		update_post_meta( $order_id, '_zotapay_status', sanitize_text_field( $response->getStatus() ) );
		update_post_meta( $order->get_id(), '_zotapay_updated', time() );
	}


	/**
	 * Add expiration time
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function set_expiration_time( $order_id ) {
		$expiration = new \DateTime();
		$expiration->add( new \DateInterval( 'PT' . \Zota_WooCommerce::ZOTAPAY_WAITING_APPROVAL . 'H' ) );
		update_post_meta( $order_id, '_zotapay_expiration', $expiration->getTimestamp() );
	}


	/**
	 * Remove expiration time
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function delete_expiration_time( $order_id ) {
		delete_post_meta( $order_id, '_zotapay_expiration' );
	}


	/**
	 * Set expired marker
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function set_expired( $order_id ) {
		$current_date = new \DateTime();
		add_post_meta( $order_id, '_zotapay_expired', $current_date->getTimestamp() );

		$message = sprintf(
			// translators: %1$s WC Order ID.
			esc_html__( 'Zotapay payment expired for order #%1$s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		// Get the order.
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$order->add_order_note( esc_html__( 'Zotapay payment expired.', 'zota-woocommerce' ) );
		$order->save();
	}


	/**
	 * Scheduled check for pending payment orders
	 *
	 * @return void
	 */
	public static function check_pending_payment_orders() {
		// Get orders.
		$args   = array(
			'posts_per_page' => 10,
			'post_type'      => 'shop_order',
			'post_status'    => 'wc-pending',
			'meta_key'       => '_zotapay_expiration',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
		);
		$orders = get_posts( $args );

		// No pending orders?
		if ( empty( $orders ) ) {
			return;
		}

		// Loop orders.
		foreach ( $orders as $order ) {
			$order_id = $order->ID;

			$message = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Pending payment check for order #%1$s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->info( $message );

			$zotapay_expiration = get_post_meta( $order_id, '_zotapay_expiration', true );

			$date_time    = new \DateTime();
			$current_time = $date_time->getTimestamp();

			if ( $zotapay_expiration < $current_time ) {
				self::delete_expiration_time( $order_id );
				self::set_expired( $order_id );
				continue;
			}

			$response = self::order_status( $order_id );

			// Update status and meta.
			self::update_status( $order_id, $response );
			update_post_meta( $order_id, '_zotapay_order_status', time() );
		}
	}
}
