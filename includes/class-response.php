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
 * Response class.
 */
class Response {

	/**
	 * Zota callback.
	 */
	public static function callback() {
		try {
			// Get the callback handler.
			$callback = new ApiCallback();

			// Get the order ID.
			$order_id = $callback->getMerchantOrderID();
			if ( null === $order_id ) {
				$error = esc_html__( 'Merchant Order ID missing.', 'zota-woocommerce' );
				Zotapay::getLogger()->error( $error );
				wp_send_json_error( $error, 400 );
			}

			// Remove test suffix.
			$order_id = Order::remove_uniqid_suffix( $callback->getMerchantOrderID() );

			// Get WC Order.
			$order = wc_get_order( $order_id );
			if ( empty( $order ) ) {
				$error = sprintf(
					// translators: %1$s order ID.
					esc_html__( 'Order with ID %1$s not found.', 'zota-woocommerce' ),
					$order_id
				);
				Zotapay::getLogger()->error( $error );
				wp_send_json_error( $error, 400 );
			}

			Zotapay::getLogger()->info(
				sprintf(
					// translators: %1$s WC Order ID, %2$s Zota Order ID.
					esc_html__( 'Callback for Order #%1$s with Zota order %2$s', 'zota-woocommerce' ),
					$order_id,
					$callback->getOrderID()
				)
			);

			// Check Order ID.
			if ( null === $callback->getOrderID() ) {
				$error = sprintf(
					// translators: %1$s Merchant Order ID.
					esc_html__( 'Merchant Order ID %1$s no Order ID.', 'zota-woocommerce' ),
					$callback->getMerchantOrderID()
				);
				Zotapay::getLogger()->error( $error );
				wp_send_json_error( $error, 400 );
			}

			// Check Status.
			if ( null === $callback->getStatus() ) {
				$error = sprintf(
					// translators: %1$s Merchant Order ID.
					esc_html__( 'Merchant Order ID %1$s no Status.', 'zota-woocommerce' ),
					$callback->getMerchantOrderID()
				);
				Zotapay::getLogger()->error( $error );
				wp_send_json_error( $error, 400 );
			}

			// Check if is the last request by matching last stored Zota order ID.
			$zotapay_order_id = $order->get_meta( '_zotapay_order_id', true );
			if ( $callback->getOrderID() !== $zotapay_order_id ) {
				$note = sprintf(
					// translators: %1$s Zota order, %2$s Zota status.
					esc_html__( 'Callback from previous payment attempt for Zota order #%1$s with status %2$s', 'zota-woocommerce' ),
					$callback->getOrderID(),
					$callback->getStatus()
				);
				$order->add_order_note( $note );
				$order->save();

				Zotapay::getLogger()->info(
					sprintf(
						// translators: %1$s Order ID, %2$s Zota order, %3$s Zota status.
						esc_html__( 'Callback from previous payment attempt for WC Order #%1$s with Zota order %2$s with status %3$s', 'zota-woocommerce' ),
						$order_id,
						$callback->getOrderID(),
						$callback->getStatus()
					)
				);
				wp_send_json_success();
			}

			// Check Processor Transaction ID.
			if ( null === $callback->getProcessorTransactionID() ) {
				Zotapay::getLogger()->info(
					sprintf(
						// translators: %1$s Merchant Order ID.
						esc_html__( 'Merchant Order ID %1$s no Processor Transaction ID.', 'zota-woocommerce' ),
						$callback->getMerchantOrderID()
					)
				);
			}

			// Update status and add notes.
			Zotapay::getLogger()->info(
				sprintf(
					// translators: %1$s Order ID, %2$s Zota order, %3$s Zota status.
					esc_html__( 'Callback update order status and add notes for WC Order #%1$s with Zota order %2$s with status %3$s', 'zota-woocommerce' ),
					$order_id,
					$callback->getOrderID(),
					$callback->getStatus()
				)
			);
			Order::handle_callback( $order_id, $callback );

			// Update order meta.
			$order->add_meta_data( '_zotapay_callback', time() );
			$order->add_meta_data( '_zotapay_transaction_id', $callback->getProcessorTransactionID() );
			$order->save();
		} catch ( InvalidSignatureException $e ) {
			// Log error.
			Zotapay::getLogger()->error( $e->getMessage() );

			// Send error.
			wp_send_json_error( $e->getMessage(), 401 );
		} catch ( ApiCallbackException $e ) {
			// Header 'HTTP/1.1 400 Bad request' sent before ApiCallbackException is thrown.
			// Log error.
			Zotapay::getLogger()->error( $e->getMessage() );
		}
	}

	/**
	 * Zota merchant redirect.
	 *
	 * @param  int $order_id Order ID.
	 * @return void
	 */
	public static function redirect( $order_id ) {
		$order = wc_get_order( $order_id );

		// If redirect is processed do nothing.
		if ( empty( $order ) || ! empty( $order->get_meta( '_zotapay_redirect', true ) ) ) {
			return;
		}

		try {

			// Get the redirect handler.
			$redirect = new MerchantRedirect();

			// Remove test prefix.
			$merchant_order_id = Order::remove_uniqid_suffix( $redirect->getMerchantOrderID() );

			// Check if order ids matching.
			if ( $order_id !== $merchant_order_id ) {
				return;
			}

			// Check if is the last request by matching last stored Zota order ID.
			$zotapay_order_id = $order->get_meta( '_zotapay_order_id', true );
			if ( $redirect->getOrderID() !== $zotapay_order_id ) {
				Zotapay::getLogger()->info(
					sprintf(
						// translators: %1$s Order ID, %2$s Zota order, %3$s Zota status.
						esc_html__( 'Merchant redirect from previous payment attempt for WC Order #%1$s with Zota order %2$s with status %3$s', 'zota-woocommerce' ),
						$order_id,
						$redirect->getOrderID(),
						$redirect->getStatus()
					)
				);
				return;
			}

			// Update status and add notes.
			Zotapay::getLogger()->info(
				sprintf(
					// translators: %1$s Order ID, %2$s Zota order, %3$s Zota status.
					esc_html__( 'Merchant redirect update order status and add notes for WC Order #%1$s with Zota order %2$s with status %3$s', 'zota-woocommerce' ),
					$order_id,
					$redirect->getOrderID(),
					$redirect->getStatus()
				)
			);
			Order::handle_redirect( $order_id, $redirect );
		} catch ( InvalidSignatureException $e ) {
			$error = sprintf(
				// translators: %1$s Order ID, %2$s Error message.
				esc_html__( 'Order ID #%1$s, %2$s', 'zota-woocommerce' ),
				$order_id,
				$e->getMessage()
			);
			Zotapay::getLogger()->error( $error );
		}
	}
}
