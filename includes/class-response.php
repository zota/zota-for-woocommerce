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
			$order_id = Order::remove_uniqid_suffix( $callback->getMerchantOrderID() );

			Zotapay::getLogger()->debug(
				sprintf(
					// translators: %1$s Order ID, %2$s Merchant Order ID.
					esc_html__( 'Callback Order #%1$s / Merchant Order ID \w test prefix #%2$s', 'zota-woocommerce' ),
					$order_id,
					$callback->getMerchantOrderID()
				)
			);

			// If callback is already processed do nothing.
			Zotapay::getLogger()->debug( esc_html__( 'Callback check if callback is alreay processed.', 'zota-woocommerce' ) );
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

			// Update status and add notes.
			Zotapay::getLogger()->info( esc_html__( 'Callback update order status and add notes.', 'zota-woocommerce' ) );
			Order::update_status( $order_id, $callback );

			// Update order meta.
			add_post_meta( $order_id, '_zotapay_callback', time() );
			add_post_meta( $order_id, '_zotapay_transaction_id', $callback->getProcessorTransactionID() );

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

		// If redirect is processed do nothing.
		if ( false === empty( get_post_meta( $order_id, '_zotapay_redirect', true ) ) ) {
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

			// Update status and add notes.
			Zotapay::getLogger()->info( esc_html__( 'Merchant redirect update order status and add notes.', 'zota-woocommerce' ) );
			Order::update_status( $order_id, $redirect );

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
