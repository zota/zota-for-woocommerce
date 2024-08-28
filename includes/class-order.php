<?php
/**
 * Zota for WooCommerce Settings
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

use \Zota\Zota_WooCommerce\Includes\Settings;
use \Zotapay\Zotapay;
use \Zotapay\DepositOrder;
use \Zotapay\OrderStatus;
use \Zotapay\OrderStatusData;
use \Zotapay\Exception\InvalidSignatureException;
use Exception;

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
	 * Register additional order statuses.
	 *
	 * @param  array $order_statuses WC Order statuses.
	 * @return array
	 */
	public static function register_shop_order_post_statuses( $order_statuses ) {
		$zota_order_statuses = array(
			'wc-partial-payment' => array(
				'label'                     => _x( 'Partial Payment', 'Order status', 'zota-woocommerce' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Partial Payment <span class="count">(%s)</span>', 'Partial Payment <span class="count">(%s)</span>', 'zota-woocommerce' ),
			),
			'wc-overpayment'    => array(
				'label'                     => _x( 'Overpayment', 'Order status', 'zota-woocommerce' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Overpayment <span class="count">(%s)</span>', 'Overpayment <span class="count">(%s)</span>', 'zota-woocommerce' ),
			),
		);

		return apply_filters( 'wc_gateway_zota_register_shop_order_post_statuses', array_merge( $order_statuses, $zota_order_statuses ) );
	}


	/**
	 * Register additional order statuses.
	 *
	 * @param  array $order_statuses WC Order statuses.
	 * @return array
	 */
	public static function valid_order_statuses_for_payment_complete( $order_statuses ) {
		$zota_order_statuses = array(
			'partial-payment',
			'overpayment',
		);

		return array_merge( $order_statuses, $zota_order_statuses );
	}


	/**
	 * Add to list of WC Order statuses.
	 *
	 * @param  array $order_statuses WC Order statuses.
	 * @return array
	 */
	public static function order_statuses( $order_statuses ) {
		$zota_order_statuses = array(
			'wc-partial-payment' => _x( 'Partial Payment', 'Order status', 'zota-woocommerce' ),
			'wc-overpayment' => _x( 'Overpayment', 'Order status', 'zota-woocommerce' ),
		);

		return array_merge( $order_statuses, $zota_order_statuses );
	}


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
		if ( ! in_array( $order->get_billing_country(), self::$requiring_states, true ) ) {
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

		// Get Zota OrderID.
		$zotapay_order_id = $order->get_meta( '_zotapay_order_id', true );
		if ( true === empty( $zotapay_order_id ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data preparation Zota OrderID (order meta) not found for WC Order #%1$s.', 'zota-woocommerce' ),
				(int) $order_id
			);

			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Get Zota MerchantOrderID.
		$zotapay_merchant_order_id = $order->get_meta( '_zotapay_merchant_order_id', true );
		if ( true === empty( $zotapay_merchant_order_id ) ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Order status data preparation Zota MerchantOrderID (order meta) not found for WC Order #%1$s.', 'zota-woocommerce' ),
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

		try {
			$order_status = new OrderStatus();
			$response     = $order_status->request( $order_status_data );
		} catch ( Exception $e ) {
			$error = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Error checking order status WC Order #%1$s: %2$s', 'zota-woocommerce' ),
				(int) $order_id,
				$e->getMessage()
			);
			Zotapay::getLogger()->error( $error );

			return false;
		}

		return $response;
	}


	/**
	 * Get order extra data.
	 *
	 * @param  \Zotapay\ApiCallback $callback API Response.
	 * @return array|false
	 */
	public static function get_extra_data( $callback ) {
		// Check extra data.
		if ( empty( $callback->getExtraData() ) ) {
			return false;
		}

		return $callback->getExtraData();
	}


	/**
	 * Is order amount changed.
	 *
	 * @param  \Zotapay\ApiCallback $callback API Response.
	 * @return array|false
	 */
	public static function amount_changed( $callback ) {
		$extra_data = self::get_extra_data( $callback );

		// If no extra data return.
		if ( empty( $extra_data ) ) {
			return false;
		}

		// Check if has amount changed key.
		if ( empty( $extra_data['amountChanged'] ) ) {
			return false;
		}

		return $extra_data['amountChanged'];
	}


	/**
	 * Add totals row for paid amount.
	 *
	 * @param int $order_id WC Order ID.
	 * @return bool
	 */
	public static function add_total_row( $order_id ) {
		// Get the order.
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			$error = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Add totals row WC Order #%s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Check order status.
		if ( ! in_array( $order->get_status(), array( 'partial-payment', 'overpayment' ), true ) ) {
			return;
		}

		// Set totals row label.
		$label = 'partial-payment' === $order->get_status() ? __( 'Partial Payment', 'zota-woocommerce' ) : __( 'Overpayment', 'zota-woocommerce' );

		// Amount changed.
		$zotapay_amount = $order->get_meta( '_zotapay_amount', true );
		?>
		<table class="wc-order-totals" style="border-top: 1px solid #999; margin-top:12px; padding-top:12px">
			<tr>
				<td class="label label-highlight"><?php echo esc_html( $label ); ?>: <br /></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wc_price( \floatval( $zotapay_amount ), array( 'currency' => $order->get_currency() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="description">
					<?php
					if ( ! empty( $order->get_date_paid() ) ) {
						if ( $order->get_payment_method_title() ) {
							/* translators: 1: payment date. 2: payment method */
							echo esc_html( sprintf( __( '%1$s via %2$s', 'zota-woocommerce' ), $order->get_date_paid()->date_i18n( get_option( 'date_format' ) ), $order->get_payment_method_title() ) );
						} else {
							echo esc_html( $order->get_date_paid()->date_i18n( get_option( 'date_format' ) ) );
						}
					}
					?>
					</span>
				</td>
				<td colspan="2"></td>
			</tr>
		</table>

		<div class="clear"></div>
		<?php
	}


	/**
	 * Handle callback.
	 *
	 * @param  int                  $order_id Order ID.
	 * @param  \Zotapay\ApiCallback $callback Callback object.
	 * @return bool
	 */
	public static function handle_callback( $order_id, $callback ) {
		// Check callback.
		if ( empty( $callback ) ) {
			$error = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Order callback empty for WC Order #%s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		$response['status']                 = $callback->getStatus();
		$response['processorTransactionID'] = $callback->getProcessorTransactionID();
		$response['errorMessage']           = $callback->getErrorMessage();

		if ( self::amount_changed( $callback ) ) {
			$extra_data = $callback->getExtraData();

			$response['amountChanged']  = true;
			$response['amount']         = $callback->getAmount();
			$response['originalAmount'] = $extra_data['originalAmount'];
		}

		return self::update_status( $order_id, $response );
	}


	/**
	 * Handle amount changed
	 *
	 * @param  \WC_order $order           WC Order.
	 * @param  array     $response        Response data.
	 * @return bool
	 */
	public static function handle_amount_changed( $order, $response ) {
		// Convert values to floats.
		$amount          = \floatval( $response['amount'] );
		$original_amount = \floatval( $response['originalAmount'] );

		// Add meta.
		$order->update_meta_data( '_zotapay_amount_changed', 'yes' );
		$order->update_meta_data( '_zotapay_amount', $amount ); // Sanitized already with floatval.

		// Order note.
		if ( $amount < $original_amount ) {
			$note = sprintf(
				// translators: %1$s amount paid, %2$s original order amount.
				esc_html__( 'Zota order partial payment. %1$s of %2$s paid.', 'zota-woocommerce' ),
				sanitize_text_field( wc_price( $amount ) ),
				sanitize_text_field( wc_price( $original_amount ) )
			);
		} elseif ( $amount > $original_amount ) {
			$note = sprintf(
				// translators: %1$s amount paid, %2$s original order amount.
				esc_html__( 'Zota order overpayment. %1$s of %2$s paid.', 'zota-woocommerce' ),
				sanitize_text_field( wc_price( $amount ) ),
				sanitize_text_field( wc_price( $original_amount ) )
			);
		} else {
			$note = esc_html__( 'Zota order payment completed.', 'zota-woocommerce' );
		}

		if ( ! empty( $response['processorTransactionID'] ) ) {
			$note .= ' ' . sprintf(
				// translators: Processor Transaction ID.
				esc_html__( 'Transaction ID: %s.', 'zota-woocommerce' ),
				sanitize_text_field( $response['processorTransactionID'] )
			);
		}

		$order->add_order_note( $note );
		$order->save();

		// Zota amount.
		$zotapay_amount = \floatval( $order->get_meta( '_zotapay_amount', true ) );

		// Compare amount paid against original amount.
		if ( $amount < $original_amount ) {
			// If amount is lower set order status to Partial Payment.
			$order->set_status( 'wc-partial-payment' );
			if ( ! $order->get_date_paid( 'edit' ) ) {
				$order->set_date_paid( time() );
			}
			$order->save();
		} elseif ( $amount > $original_amount ) {
			// If amount is greater set order status to Overpaid.
			$order->set_date_paid( time() );
			$order->set_status( 'wc-overpayment' );
			if ( ! $order->get_date_paid( 'edit' ) ) {
				$order->set_date_paid( time() );
			}
			$order->save();
		} else {
			// If amount equals to original amount set order payment complete.
			self::delete_expiration_time( $order->get_id() );
			$order->payment_complete();
		}

		return true;
	}


	/**
	 * Handle merchant redirect.
	 *
	 * @param  int                       $order_id Order ID.
	 * @param  \Zotapay\MerchantRedirect $redirect Redirect object.
	 * @return bool
	 */
	public static function handle_redirect( $order_id, $redirect ) {

		// Check callback.
		if ( empty( $redirect ) ) {
			$error = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Order redirect empty for WC Order #%s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		$response['status']                 = $redirect->getStatus();
		$response['errorMessage']           = $redirect->getErrorMessage();

		return self::update_status( $order_id, $response );
	}


	/**
	 * Process order status response.
	 *
	 * @param  int   $order_id Order ID.
	 * @param  array $response Response data.
	 * @return bool
	 */
	public static function update_status( $order_id, $response ) {
		// Get the order.
		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			$error = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Update status WC Order #%s not found.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->error( $error );
			return false;
		}

		// Update order meta.
		$order->update_meta_data( '_zotapay_status', sanitize_text_field( $response['status'] ) );
		$order->update_meta_data( '_zotapay_updated', time() );
		$order->save();

		$message = sprintf(
			// translators: %s WC Order ID.
			esc_html__( 'Update status for WC Order #%s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		// Awaiting statuses.
		if ( in_array( $response['status'], array( 'CREATED', 'PENDING', 'PROCESSING' ), true ) ) {
			$note = sprintf(
				// translators: Zota status.
				esc_html__( 'Zota status: %s.', 'zota-woocommerce' ),
				esc_html( $response['status'] )
			);
			$order->add_order_note( $note );
			$order->save();
			return false;
		}

		// Status APPROVED.
		if ( 'APPROVED' === $response['status'] ) {
			self::delete_expiration_time( $order_id );

			// Check is amount changed.
			if ( isset( $response['amountChanged'] ) ) {
				return self::handle_amount_changed( $order, $response );
			}

			if ( ! empty( $response['processorTransactionID'] ) ) {
				$note = sprintf(
					// translators: %1$s Zota status, %2$s Processor Transaction ID.
					esc_html__( 'Zota status: %1$s, Transaction ID: %2$s.', 'zota-woocommerce' ),
					sanitize_text_field( $response['status'] ),
					sanitize_text_field( $response['processorTransactionID'] )
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

		// Status UNKNOWN send an email to Zota, log error and add order note.
		if ( 'UNKNOWN' === $response['status'] ) {

			// Log info.
			$log = sprintf(
				// translators: %s WooCommerce Order.
				esc_html__( 'WooCommerce Order: %s', 'zota-woocommerce' ),
				$order->get_id()
			);
			Zotapay::getLogger()->info( $log );

			$message = sprintf(
				// translators: %1$s Zota email, %2$s Status.
				esc_html__( 'You are receiving this because order has status %1$s. Please forward this email to %2$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response['status'] ),
				'support@zotapay.com'
			);
			$message .= PHP_EOL . PHP_EOL . $log;

			$note = sprintf(
				// translators: %1$s Zota status, %2$s Processor Transaction ID.
				esc_html__( 'Zota status: %1$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response['status'] )
			);

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
		if ( ! empty( $response['processorTransactionID'] ) ) {
			$note = sprintf(
				// translators: %1$s Zota status, %2$s Processor Transaction ID, %3$s Error message.
				esc_html__( 'Zota status: %1$s, Transaction ID: %2$s, Error: %3$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response['status'] ),
				sanitize_text_field( $response['processorTransactionID'] ),
				sanitize_text_field( $response['errorMessage'] )
			);
		} else {
			$note = sprintf(
				// translators: %1$s Zota status, %2$s Error message.
				esc_html__( 'Zota status: %1$s, Error: %2$s.', 'zota-woocommerce' ),
				sanitize_text_field( $response['status'] ),
				sanitize_text_field( $response['errorMessage'] )
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
			esc_html__( 'Zota payment expired for order #%1$s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		$order->add_order_note( esc_html__( 'Zota payment expired.', 'zota-woocommerce' ) );
		$order->save();
	}


	/**
	 * Check order status by order ID and schedule next check if status hasn't changed to a final type.
	 *
	 * @param int $order_id Order ID.
	 * @return void
	 */
	public static function check_status( $order_id ) {

		// Zota Configuration.
		Settings::init();

		// Logging treshold.
		$settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );
		if ( 'yes' === $settings['logging'] ) {
			Zotapay::setLogThreshold( Settings::log_treshold() );
		}

		$message = sprintf(
			// translators: %s WC Order ID.
			esc_html__( 'Check status started WC Order #%s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		$order = wc_get_order( $order_id );
		if ( empty( $order ) ) {
			$message = sprintf(
				// translators: %1$s WC Order ID.
				esc_html__( 'Check order status for WC Order #%s failed, order not found.', 'zota-woocommerce' ),
				$order_id
			);
			Zotapay::getLogger()->error( $message );
			return;
		}

		// If order is paid do nothing.
		if ( $order->is_paid() ) {
			$message = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Check status ended for paid WC Order #%s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->info( $message );
			return;
		}

		// If partial/overpayment do nothing.
		if ( in_array( $order->get_status(), array( 'partial-payment', 'overpayment' ), true ) ) {
			return;
		}

		// If expired do nothing.
		if ( ! empty( $order->get_meta( '_zotapay_expired', true ) ) ) {
			$message = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Check status ended for expired WC Order #%s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->info( $message );
			return;
		}

		$message = sprintf(
			// translators: %s WC Order ID.
			esc_html__( 'Checking expiration time for WC Order #%s.', 'zota-woocommerce' ),
			(int) $order_id
		);
		Zotapay::getLogger()->info( $message );

		$zotapay_expiration    = intval( $order->get_meta( '_zotapay_expiration', true ) );
		$zotapay_status_checks = intval( $order->get_meta( '_zotapay_status_checks', true ) );

		$date_time    = new \DateTime();
		$current_time = $date_time->getTimestamp();

		if ( $zotapay_expiration < $current_time ) {
			self::delete_expiration_time( $order_id );
			self::set_expired( $order_id );

			$message = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'WC Order #%s expired.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->info( $message );
			return;
		}

		$order_status = self::order_status( $order_id );

		$response['status']                 = $order_status->getStatus();
		$response['errorMessage']           = $order_status->getErrorMessage();

		// Update status and meta.
		if ( ! self::update_status( $order_id, $response ) ) {
			$zotapay_status_checks++;
			$order->update_meta_data( '_zotapay_status_checks', $zotapay_status_checks );

			// Increase the interval between checks exponentialy, but not more than a day.
			$next_time = time() + min( 5 * MINUTE_IN_SECONDS * pow( 2, $zotapay_status_checks ), DAY_IN_SECONDS );

			if ( class_exists( 'ActionScheduler' ) ) {
				as_schedule_single_action( $next_time, 'zota_scheduled_order_status', array( $order_id ), ZOTA_WC_GATEWAY_ID );
			} else {
				wp_schedule_single_event( $next_time, 'zota_scheduled_order_status', array( $order_id ) );
			}

			$message = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Scheduled action added on status check for WC Order #%s.', 'zota-woocommerce' ),
				(int) $order_id
			);
			Zotapay::getLogger()->info( $message );
		}

		$order->update_meta_data( '_zotapay_order_status', time() );
		$order->save();
	}


	/**
	 * Add column adjustment
	 *
	 * @param array $columns Columns list.
	 *
	 * @return array
	 */
	public static function admin_columns( $columns ) {
		$settings = get_option( 'woocommerce_' . ZOTA_WC_GATEWAY_ID . '_settings', array() );
		if ( ! isset( $settings['column_order_id'] ) || 'yes' !== $settings['column_order_id'] ) {
			return $columns;
		}

		$columns = array_slice( $columns, 0, 2, true )
		+ array( 'zotapay-order-id' => esc_html__( 'Zota OrderID', 'zota-woocommerce' ) )
		+ array_slice( $columns, 1, null, true );

		return $columns;
	}


	/**
	 * Show Zota Order ID in column
	 *
	 * @param string $column Admin column.
	 * @param string $post_id Post ID.
	 *
	 * @return void
	 */
	public static function admin_column_order_id( $column, $post_id ) {
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

			echo esc_html(
				! empty( $zotapay_order_id ) ? $zotapay_order_id : 'n/a'
			);
		}
	}
}
