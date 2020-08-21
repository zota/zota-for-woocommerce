<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zotapay\ApiCallback;
use \Zotapay\MerchantRedirect;
use \Zotapay\Exception\InvalidSignatureException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zotapay Response class.
 */
class Zotapay_Response {

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

		// If order is paid do nothing.
		if ( $order->is_paid() ) {
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
			if ( preg_match( '/(.*)-test-(.*)/', $redirect->getMerchantOrderID(), $matches ) === 1 ) {
				if ( ! empty( $matches[1] ) ) {
					$merchant_order_id = (int) $matches[2];
				}
			}

			// Check if order ids matching.
			if ( $order_id !== $merchant_order_id ) {
				return;
			}

			// Update order meta.
			add_post_meta( $order_id, '_zotapay_redirect', time() );
			add_post_meta( $order_id, '_zotapay_merchant_order_id', sanitize_text_field( $redirect->getMerchantOrderID() ) );
			add_post_meta( $order_id, '_zotapay_order_id', sanitize_text_field( $redirect->getOrderID() ) );
			add_post_meta( $order_id, '_zotapay_status', sanitize_text_field( $redirect->getStatus() ) );

			// Status PENDING.
			if ( 'PENDING' === $redirect->getStatus() ) {
				return;
			}

			// Status APPROVED.
			if ( 'APPROVED' === $redirect->getStatus() ) {
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
			Logger::info( $error );
		}
	}

	/**
	 * Zotapay callback.
	 */
	public function callback() {
	}
}
