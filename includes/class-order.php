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
	 * Countries that require customer state.
	 *
	 * @var array
	 */
	public static $requiring_states = array( 'AU', 'CA', 'US' );


	/**
	 * Prepare customer state.
	 *
	 * @param  WC_Order $order WC Order.
	 * @return string
	 */
	public static function get_billing_state( $order ) {

		if ( empty( $order->get_billing_state() ) ) {
			return '';
		}

		// Check if country requires customer state.
		if ( ! in_array( $order->get_billing_country(), self::$requiring_states ) ) {
			return '';
		}

		// Australian states from WooCommerce format to Zota required format.
		if ( 'AU' === $order->get_billing_country() ) {
			return \substr( $order->get_billing_state(), 0, 2 );
		}

		return $order->get_billing_state();
	}


	/**
	 * Prepare deposit request data.
	 *
	 * @param  int $order_id Order ID.
	 * @return \Zotapay\DepositOrder|false
	 */
	public static function deposit_order( $order_id ) {

		// Get WC Order.
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Deposit order WC Order #%1$s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Set merchantOrderID.
		$merchant_order_id = (string) $order->get_id();

		// If test mode enabled orf is payment attempt for already created order add uniqid suffix.
		if ( Settings::$testmode || ( isset( $_GET['pay_for_order'] ) && 'true' === $_GET['pay_for_order'] ) ) { // phpcs:ignore
			$merchant_order_id = self::add_uniqid_suffix( $order->get_id() );
		}

		$deposit_order = new DepositOrder();

		$deposit_order->setMerchantOrderID( $merchant_order_id );
		$deposit_order->setMerchantOrderDesc( ZOTA_WC_NAME . '. Order Number: ' . $order->get_order_number() );
		$deposit_order->setOrderAmount( number_format( $order->get_total(), 2, '.', '' ) );
		$deposit_order->setOrderCurrency( $order->get_currency() );
		$deposit_order->setCustomerEmail( $order->get_billing_email() );
		$deposit_order->setCustomerFirstName( $order->get_billing_first_name() );
		$deposit_order->setCustomerLastName( $order->get_billing_last_name() );
		$deposit_order->setCustomerAddress( $order->get_billing_address_1() );
		$deposit_order->setCustomerCountryCode( $order->get_billing_country() );
		$deposit_order->setCustomerCity( $order->get_billing_city() );
		$deposit_order->setCustomerState( self::get_billing_state( $order ) );
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
		if ( empty( $order ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data WC Order #%1$s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Get Zotapay OrderID.
		$zotapay_order_id = $order->get_meta( '_zotapay_order_id', true );
		if ( true === empty( $zotapay_order_id ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data preparation Zotapay OrderID (order meta) not found for WC Order #%1$s.', 'zota-woocommerce' ),
				(int) $order_id
			);

			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Get Zotapay MerchantOrderID.
		$zotapay_merchant_order_id = $order->get_meta( '_zotapay_merchant_order_id', true );
		if ( true === empty( $zotapay_merchant_order_id ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data preparation Zotapay MerchantOrderID (order meta) not found for WC Order #%1$s.', 'zota-woocommerce' ),
				(int) $order_id
			);

			Zotapay::getLogger()->error( $error );
			return false;
		}

		$order_status_data = new OrderStatusData();

		// Set orderID.
		$order_status_data->setOrderID( $zotapay_order_id );
		$order_status_data->setMerchantOrderID( $zotapay_merchant_order_id );

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
	 * @param  int                  $order_id Order ID.
	 * @param  \Zotapay\ApiResponse $response Response Status.
	 * @return bool
	 */
	public static function update_status( $order_id, $response ) {

		// Get the order.
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Update status WC Order #%1$s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Check response.
		if ( false === $response ) {
			return false;
		}

		// If no change do nothing.
		if ( $order->get_meta( '_zotapay_status', true ) === $response->getStatus() ) {
			return false;
		}

		// Update order meta.
		$order->update_meta_data( '_zotapay_status', sanitize_text_field( $response->getStatus() ) );
		$order->update_meta_data( '_zotapay_updated', time() );
		$order->save();

		// Awaiting statuses.
		if ( in_array( $response->getStatus(), array( 'CREATED', 'PENDING', 'PROCESSING' ), true ) ) {
			$note = sprintf(
				// translators: Zotapay status.
				esc_html__( 'Zotapay status: %s.', 'zota-woocommerce' ),
				sanitize_text_field( $response->getStatus() )
			);
			$order->add_order_note( $note );
			$order->save();
			return false;
		}

		// Status APPROVED.
		if ( 'APPROVED' === $response->getStatus() ) {

			// Delete expiration time.
			self::delete_expiration_time( $order_id );

			if ( method_exists( $response, 'getProcessorTransactionID' ) ) {
				$note = sprintf(
					// translators: %1$s Zotapay status, %2$s Processor Transaction ID.
					esc_html__( 'Zotapay status: %1$s, Transaction ID: %2$s.', 'zota-woocommerce' ),
					sanitize_text_field( $response->getStatus() ),
					sanitize_text_field( $response->getProcessorTransactionID() )
				);
				$order->add_order_note( $note );
				$order->save();
			}

			// If order is paid do nothing.
			if ( $order->is_paid() ) {
				return true;
			}

			$order->payment_complete();
			return true;
		}

		// Status UNKNOWN send an email to Zotapay, log error and add order note.
		if ( 'UNKNOWN' === $response->getStatus() ) {

			// Log info.
			$log = sprintf(
				// translators: %s WooCommerce Order.
				esc_html__( 'WooCommerce Order: %s', 'zota-woocommerce' ),
				$order->get_id()
			);
			Zotapay::getLogger()->info( $log );

			$message = sprintf(
				// translators: %1$s Zotapay email, %2$s Status.
				esc_html__( 'You are receiving this because order has status %1$s. Please forward this email to %2$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response->getStatus() ),
				'support@zotapay.com'
			);
			$message .= PHP_EOL . PHP_EOL . $log;
			$message .= PHP_EOL . PHP_EOL . $note;

			// Send email to admin.
			$wp_mail = wp_mail( get_option( 'admin_email' ), ZOTA_WC_NAME, $message );
			if ( false === $wp_mail ) {
				$error = esc_html__( 'Send email to admin failed.', 'zota-woocommerce' );
				Zotapay::getLogger()->error( $error . ' ' . $log . ', ' . $note );
			}

			// Log info.
			Zotapay::getLogger()->info( $note );

			// Add order note.
			$order->add_order_note( $note );
			$order->save();
			return false;
		}

		// Final statuses with errors - DECLINED, FILTERED, ERROR.
		if ( method_exists( $response, 'getProcessorTransactionID' ) ) {
			$note = sprintf(
				// translators: %1$s Zotapay status, %2$s Processor Transaction ID, %3$s Error message.
				esc_html__( 'Zotapay status: %1$s, Transaction ID: %2$s, Error: %3$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response->getStatus() ),
				sanitize_text_field( $response->getProcessorTransactionID() ),
				sanitize_text_field( $response->getErrorMessage() )
			);
		} else {
			$note = sprintf(
				// translators: %1$s Zotapay status, %2$s Error message.
				esc_html__( 'Zotapay status: %1$s, Error: %2$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response->getStatus() ),
				sanitize_text_field( $response->getErrorMessage() )
			);
		}
		$order->update_status( 'failed', $note );

		return true;
	}


	/**
	 * Add uniqid to Order ID.
	 *
	 * @param  int $order_id Order ID.
	 * @return int
	 */
	public static function add_uniqid_suffix( $order_id ) {
		return (string) $order_id . '-uniqid-' . uniqid();
	}


	/**
	 * Remove uniqid uniqid from Order ID.
	 *
	 * @param  int $order_id Order ID.
	 * @return int
	 */
	public static function remove_uniqid_suffix( $order_id ) {
		if ( preg_match( '/(.*)-uniqid-(.*)/', $order_id, $matches ) === 1 ) {
			if ( ! empty( $matches[1] ) ) {
				return (int) $matches[1];
			}
		}
		return $order_id;
	}


	/**
	 * Add expiration time
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function set_expiration_time( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			return;
		}

		$expiration = new \DateTime();
		$expiration->add( new \DateInterval( 'PT' . \Zota_WooCommerce::ZOTAPAY_WAITING_APPROVAL . 'H' ) );
		$order->update_meta_data( '_zotapay_expiration', $expiration->getTimestamp() );
		$order->save();
	}


	/**
	 * Remove expiration time
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function delete_expiration_time( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			return;
		}
		$order->delete_meta_data( '_zotapay_expiration' );
		$order->save();
	}


	/**
	 * Set expired marker
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function set_expired( $order_id ) {
		$current_date = new \DateTime();

		// Get the order.
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			return;
		}

		$order->add_meta_data( '_zotapay_expired', $current_date->getTimestamp() );

		$message = sprintf(
			// translators: %1$s WC Order ID.
			esc_html__( 'Zotapay payment expired for order #%1$s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		$order->add_order_note( esc_html__( 'Zotapay payment expired.', 'zota-woocommerce' ) );
		$order->save();
	}


	/**
	 * Check order status by order ID and schedule next check if status hasn't changed to a final type.
	 *
	 * @param int $order_id Order ID
	 * @return void
	 */
	public static function check_status( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			$message = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Check order status for order #%1$d failed, order not found.', 'zota-woocommerce' ),
				$order_id
			);
			Zotapay::getLogger()->error( $message );
			return;
		}

		// If order is paid do nothing.
		if ( $order->is_paid() ) {
			return;
		}

		$message = sprintf(
			// translators: %1$s WC Order ID.
			esc_html__( 'Check order status for order #%1$s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		$zotapay_expiration = $order->get_meta( '_zotapay_expiration', true );
		$zotapay_status_checks = intval( $order->get_meta( '_zotapay_status_checks', true ) );

		$date_time    = new \DateTime();
		$current_time = $date_time->getTimestamp();

		if ( $zotapay_expiration < $current_time ) {
			self::delete_expiration_time( $order_id );
			self::set_expired( $order_id );
			return;
		}

		$response = self::order_status( $order_id );

		// Debug
		Zotapay::getLogger()->debug( print_r( $response, true ) );

		// Update status and meta.
		if ( ! self::update_status( $order_id, $response ) ) {
			$zotapay_status_checks++;
			$order->update_meta_data( '_zotapay_status_checks', $zotapay_status_checks );

			// Increase the interval between checks exponentialy, but not more than a day.
			$next_time = time() + min( 5 * MINUTE_IN_SECONDS * pow( 2, $zotapay_status_checks ), DAY_IN_SECONDS );

			if ( class_exists( 'ActionScheduler' ) ) {
				as_schedule_single_action( $next_time, 'zota_scheduled_order_status', [ $order_id ], ZOTA_WC_GATEWAY_ID );
			} else {
				wp_schedule_single_event( $next_time, 'zota_scheduled_order_status', [ $order_id ] );
			}
		}

		$order->update_meta_data( '_zotapay_order_status', time() );
		$order->save();
	}


	/**
     * Add column column adjustment
     *
     * @param array $columns Columns list.
     *
     * @return array
     */
	function admin_columns( $columns )
	{
		$settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );
		if ( 'yes' !== $settings['column_order_id'] ) {
			return $columns;
		}

		$columns = array_slice( $columns, 1, 1, true )
		+ array( 'zotapay-order-id' => esc_html__( 'ZotaPay OrderID', 'zota-woocommerce' ) )
		+ array_slice( $columns, 1, null, true );

	    return $columns;
	}


    /**
     * Show ZotaPay Order ID in column
     *
	 * @param string $column Admin column.
     * @param string $post_id Post ID.
	 *
	 * @return void
     */
    public static function admin_column_order_id( $column, $post_id )
    {
		if ( 'zotapay-order-id' === $column ) {

			$settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );
			if ( 'yes' !== $settings['column_order_id'] ) {
				return;
			}

			$order = wc_get_order( $post_id );
			if ( ! $order ) {
				return;
			}

			$zotapay_order_id = $order->get_meta( '_zotapay_order_id', true );

			echo ! empty( $zotapay_order_id ) ? $zotapay_order_id : 'n/a';
		}
    }
}
