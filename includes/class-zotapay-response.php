<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zotapay\Zotapay;
use \Zotapay\ApiCallback;
use \Zotapay\MerchantRedirect;
use \Zotapay\Exception\InvalidSignatureException;
use \Zotapay\Exception\ApiCallbackException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zotapay Response class.
 */
class Zotapay_Response {

	/**
	 * Zotapay callback.
	 */
	public static function callback() {

		try {
			// Get the callback handler.
			$callback = new ApiCallback();

			// Get the order ID.
			$order_id = $callback->getMerchantOrderID();
			if ( null === $order_id ) {
				$error = esc_html__( 'Merchant Order ID missing.', 'zota-woocommerce' );
				wp_send_json_error( $error, 400 );
				Zotapay::getLogger()->error( $error );
				return;
			}

			// Remove test prefix.
			$order_id = Settings::remove_test_prefix( $callback->getMerchantOrderID() );

			Zotapay::getLogger()->info(
				sprintf(
					// translators: %1$s Order ID, %2$s Merchant Order ID.
					esc_html__( 'Callback Order ID #%1$s / Merchant Order ID \w test prefix #%2$s', 'zota-woocommerce' ),
					$order_id,
					$callback->getMerchantOrderID()
				)
			);

			// Get WC Order.
			Zotapay::getLogger()->info( esc_html__( 'Callback get WC Order.', 'zota-woocommerce' ) );
			$order = wc_get_order( $order_id );
			if ( false === $order_id ) {
				$error = sprintf(
					// translators: %1$s WC Order ID.
					esc_html__( 'Zotapay Callback Order #%1$s not found.', 'zota-woocommerce' ),
					(int) $order_id
				);
				Zotapay::getLogger()->error( $error );
				return;
			}

			// If callback is already processed do nothing.
			Zotapay::getLogger()->info( esc_html__( 'Callback check if callback is alreay processed.', 'zota-woocommerce' ) );
			if ( false === empty( get_post_meta( $order_id, '_zotapay_callback', true ) ) ) {
				return;
			}

			// Check Status.
			if ( null === $callback->getStatus() ) {
				$error = sprintf(
					// translators: %1$s Merchant Order ID.
					esc_html__( 'Merchant Order ID %1$s no Status.', 'zota-woocommerce' ),
					$callback->getMerchantOrderID()
				);
				wp_send_json_error( $error, 400 );
				Zotapay::getLogger()->error( $error );
				return;
			}

			// Check Processor Transaction ID.
			if ( null === $callback->getProcessorTransactionID() ) {
				$error = sprintf(
					// translators: %1$s Merchant Order ID.
					esc_html__( 'Merchant Order ID %1$s no Processor Transaction ID.', 'zota-woocommerce' ),
					$callback->getMerchantOrderID()
				);
				wp_send_json_error( $error, 400 );
				Zotapay::getLogger()->error( $error );
				return;
			}

			// Check Order ID.
			if ( null === $callback->getOrderID() ) {
				$error = sprintf(
					// translators: %1$s Merchant Order ID.
					esc_html__( 'Merchant Order ID %1$s no Order ID.', 'zota-woocommerce' ),
					$callback->getMerchantOrderID()
				);
				wp_send_json_error( $error, 400 );
				Zotapay::getLogger()->error( $error );
				return;
			}

			// Update order meta.
			Zotapay::getLogger()->info( esc_html__( 'Callback update order meta.', 'zota-woocommerce' ) );
			add_post_meta( $order_id, '_zotapay_callback', time() );
			add_post_meta( $order_id, '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
			if ( true === empty( get_post_meta( $order_id, '_zotapay_merchant_order_id', true ) ) ) {
				add_post_meta( $order_id, '_zotapay_merchant_order_id', sanitize_text_field( $callback->getMerchantOrderID() ) );
			}
			if ( true === empty( get_post_meta( $order_id, '_zotapay_order_id', true ) ) ) {
				add_post_meta( $order_id, '_zotapay_order_id', sanitize_text_field( $callback->getOrderID() ) );
			}
			update_post_meta( $order_id, '_zotapay_status', sanitize_text_field( $callback->getStatus() ) );
			update_post_meta( $order_id, '_zotapay_updated', time() );

			Zotapay::getLogger()->info( esc_html__( 'Callback update order status and add notes.', 'zota-woocommerce' ) );

			// Status PENDING.
			if ( 'PENDING' === $callback->getStatus() ) {
				return;
			}

			// Status APPROVED.
			if ( 'APPROVED' === $callback->getStatus() ) {
				$note = sprintf(
					// translators: %1$s Processor Transaction ID, %2$s OrderID.
					esc_html__( 'Zotapay Processor Transaction ID: %1$s, OrderID: %2$s.', 'zota-woocommerce' ),
					sanitize_text_field( $callback->getProcessorTransactionID() ),
					sanitize_text_field( $callback->getOrderID() )
				);
				$order->add_order_note( $note );
				$order->save();

				// If order is paid do nothing.
				if ( $order->is_paid() ) {
					return;
				}

				$order->payment_complete();
				return;
			}

			// Add order note with the status and error message.
			$note = sprintf(
				// translators: %1$s Processor Transaction ID, %2$s OrderID, %3$s Status, %4$s Error message.
				esc_html__( 'Zotapay Processor Transaction ID: %1$s, OrderID: %2$s, Status: %3$s, Error: %4$s.', 'zota-woocommerce' ),
				sanitize_text_field( $callback->getProcessorTransactionID() ),
				sanitize_text_field( $callback->getOrderID() ),
				sanitize_text_field( $callback->getStatus() ),
				sanitize_text_field( $callback->getErrorMessage() )
			);
			$order->update_status( 'failed', $note );

		} catch ( InvalidSignatureException $e ) {
			// Send error.
			wp_send_json_error( $e->getMessage(), 401 );

			// Log error.
			Zotapay::getLogger()->debug( $e->getMessage() );
		} catch ( ApiCallbackException $e ) {
			// Log error.
			Zotapay::getLogger()->debug( $e->getMessage() );
		}
	}

	/**
	 * Zotapay merchant redirect.
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function redirect( $order_id ) {

		// Get the order.
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// If redirect is processed do nothing.
		if ( false === empty( get_post_meta( $order_id, '_zotapay_redirect', true ) ) ) {
			return;
		}

		try {

			// Get the redirect handler.
			$redirect = new MerchantRedirect();

			// Remove test prefix.
			$merchant_order_id = Settings::remove_test_prefix( $redirect->getMerchantOrderID() );

			// Check if order ids matching.
			if ( $order_id !== $merchant_order_id ) {
				return;
			}

			// Update order meta.
			add_post_meta( $order_id, '_zotapay_redirect', time() );
			if ( true === empty( get_post_meta( $order_id, '_zotapay_merchant_order_id', true ) ) ) {
				add_post_meta( $order_id, '_zotapay_merchant_order_id', sanitize_text_field( $redirect->getMerchantOrderID() ) );
			}
			if ( true === empty( get_post_meta( $order_id, '_zotapay_order_id', true ) ) ) {
				add_post_meta( $order_id, '_zotapay_order_id', sanitize_text_field( $redirect->getOrderID() ) );
			}
			update_post_meta( $order_id, '_zotapay_status', sanitize_text_field( $redirect->getStatus() ) );
			update_post_meta( $order_id, '_zotapay_updated', time() );

			// Status PENDING.
			if ( 'PENDING' === $redirect->getStatus() ) {
				return;
			}

			// Status APPROVED.
			if ( 'APPROVED' === $redirect->getStatus() ) {

				// If order is paid do nothing.
				if ( $order->is_paid() ) {
					return;
				}

				$order->payment_complete();
				return;
			}

			// Add order note with the status and error message.
			$note = sprintf(
				// translators: %1$s OrderID, %2$s Status, %3$s Error message.
				esc_html__( 'Zotapay OrderID: %1$s, Status: %2$s, Error: %3$s.', 'zota-woocommerce' ),
				sanitize_text_field( $redirect->getOrderID() ),
				sanitize_text_field( $redirect->getStatus() ),
				sanitize_text_field( $redirect->getErrorMessage() )
			);
			$order->update_status( 'failed', $note );

		} catch ( InvalidSignatureException $e ) {
			$error = sprintf(
				// translators: %1$s Order ID, %2$s Error message.
				esc_html__( 'Order ID #%1$s, %2$s', 'zota-woocommerce' ),
				$order_id,
				$e->getMessage()
			);
			Zotapay::getLogger()->debug( $error );
		}
	}
}
