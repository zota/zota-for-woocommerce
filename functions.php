<?php

use \Zota\Zota_WooCommerce\Includes\Order;
use \Zota\Zota_WooCommerce\Includes\Settings;
use \Zotapay\Zotapay;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Check if requirements are ok.
 *
 * @return bool
 */
function wc_gateway_zota_requirements() {
	$woocommerce_version = version_compare( get_option( 'woocommerce_db_version' ), ZOTA_WC_MIN_WC_VER, '>=' );
	$php_version         = version_compare( PHP_VERSION, ZOTA_WC_MIN_PHP_VER, '>=' );

	return $woocommerce_version && $php_version;
}


/**
 * Requirements error message.
 *
 * @return void
 */
function wc_gateway_zota_requirements_error() {
	global $pagenow;

	if ( $pagenow !== 'plugins.php' ) {
		return;
	}

	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				// translators: %1$s is plugin name, %2$s is PHP version, %3$s is WooCommerce version.
				esc_html__( '%1$s needs PHP version %2$s and WooCommerce version %3$s or newer.', 'zota-woocommerce' ),
				'<strong>' . esc_html( ZOTA_WC_NAME ) . '</strong>',
				esc_html( ZOTA_WC_MIN_PHP_VER ),
				esc_html( ZOTA_WC_MIN_WC_VER )
			);
			?>
			</strong>
		</p>
	</div>
	<?php
}

/**
 * WooCommerce not activated error message.
 *
 * @return void
 */
function wc_gateway_zota_woocommerce_error() {
	global $pagenow;

	if ( $pagenow !== 'plugins.php' ) {
		return;
	}

	?>
	<div class="notice notice-warning">
		<p>
			<?php
			printf(
				// translators: %1$s is plugin name.
				esc_html__( '%1$s requires WooCommerce to be active.', 'zota-woocommerce' ),
				'<strong>' . esc_html( ZOTA_WC_NAME ) . '</strong>'
			);
			?>
			</strong>
		</p>
	</div>
	<?php
}

/**
 * Plugin initialization.
 *
 * @return void
 */
function zota_plugin_init() {
	// Load the textdomain.
	load_plugin_textdomain( 'zota-woocommerce', false, plugin_basename( dirname( __FILE__, 2 ) ) . '/languages' );

	// Show admin notice if WooCommerce is not active.
	if ( ! class_exists( 'woocommerce' ) ) {
		add_action( 'admin_notices', 'wc_gateway_zota_woocommerce_error' );
	}
}

/**
 * Gateway init.
 *
 * @return void
 */
function wc_gateway_zota_init() {

	// Initialize.
	require_once ZOTA_WC_PATH . '/includes/class-zota-woocommerce.php';

	// Add to woocommerce payment gateways.
	add_filter(
		'woocommerce_payment_gateways',
		function ( $methods ) {
			$methods[] = 'Zota_WooCommerce';
			return $methods;
		}
	);

	// Scheduled check for pending payments.
	add_action( 'zota_scheduled_order_status', array( '\Zota\Zota_WooCommerce\Includes\Order', 'check_status' ), 10, 1 );
}


/**
 * Deactivate plugin.
 *
 * @return void
 */
function wc_gateway_zota_deactivate() {

	// Check requirements
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		return;
	}

	// Zotapay Configuration.
	Settings::init();

	// Logging treshold.
	Settings::log_treshold();

	Zotapay::getLogger()->info( esc_html__( 'Deactivation started.', 'zota-woocommerce' ) );

	// Get orders.
	$args = array(
		'posts_per_page' => -1,
		'post_type'      => 'shop_order',
		'post_status'    => 'wc-pending',
		'meta_key'       => '_zotapay_expiration', // phpcs:ignore
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'fields'         => 'ids',
	);
	$orders = get_posts( $args );

	// No pending orders?
	if ( empty( $orders ) ) {
		Zotapay::getLogger()->info( esc_html__( 'No pending orders.', 'zota-woocommerce' ) );
		Zotapay::getLogger()->info( esc_html__( 'Deactivation finished.', 'zota-woocommerce' ) );
		return;
	}

	// Loop orders.
	foreach ( $orders as $order_id ) {

		$order = wc_get_order( $order_id );

		if ( empty( $order ) ) {
			Zotapay::getLogger()->debug( esc_html__( $order_id . ' Order not found.', 'zota-woocommerce' ) );
			continue;
		}

		// Order status.
		$response = Order::order_status( $order_id );
		if ( false === $response ) {
			$error = sprintf(
				// translators: %s WC Order ID.
				esc_html__( 'Order Status failed for order #%s ', 'zota-woocommerce' ),
				$order_id
			);
			Zotapay::getLogger()->info( $error );
			continue;
		}
		if ( null !== $response->getMessage() ) {
			$error = sprintf(
				// translators: %1$s WC Order ID, %2$s Error message.
				esc_html__( 'Order Status failed for order #%1$s. Error: %2$s', 'zota-woocommerce' ),
				$order_id,
				$response->getMessage()
			);
			Zotapay::getLogger()->info( $error );
			continue;
		}

		if ( 'APPROVED' !== $response->getStatus() ) {
			Order::delete_expiration_time( $order_id );
			Order::set_expired( $order_id );
			continue;
		}

		// Update status and meta.
		Order::update_status( $order_id, $response );
		$order->update_meta_data( '_zotapay_order_status', time() );
		$order->save();
	}

	Zotapay::getLogger()->info( esc_html__( 'Deactivation finished.', 'zota-woocommerce' ) );
}
