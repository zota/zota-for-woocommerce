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

	// Enqueue scripts.
	add_action( 'admin_enqueue_scripts', 'zota_admin_enqueue_scripts' );

	// WooCommerce settings tab.
	add_filter( 'woocommerce_settings_tabs_array', [ '\Zota\Zota_WooCommerce\Includes\Settings', 'settings_tab' ], 50 );
	add_action( 'woocommerce_settings_tabs_' . ZOTA_WC_PLUGIN_ID, [ '\Zota\Zota_WooCommerce\Includes\Settings', 'settings_show' ] );
	add_action( 'woocommerce_update_options_' . ZOTA_WC_PLUGIN_ID, [ '\Zota\Zota_WooCommerce\Includes\Settings', 'settings_update' ] );
	add_action( 'woocommerce_save_settings_' . ZOTA_WC_PLUGIN_ID, [ '\Zota\Zota_WooCommerce\Includes\Settings', 'save_settings' ] );
	add_action( 'woocommerce_admin_field_icon', [ '\Zota\Zota_WooCommerce\Includes\Settings', 'field_icon' ], 10, 1 );
	add_action( 'woocommerce_admin_field_remove_payment_method', [ '\Zota\Zota_WooCommerce\Includes\Settings', 'field_remove_payment_method' ], 10, 1 );
	add_action( 'wp_ajax_add_payment_method', [ '\Zota\Zota_WooCommerce\Includes\Settings', 'add_payment_method' ] );

	// Initialize.
	require_once ZOTA_WC_PATH . '/includes/class-zota-woocommerce.php';

	// Add to woocommerce payment gateways.
	add_filter(
		'woocommerce_payment_gateways',
		function ( $gateways ) {

			$payment_methods = get_option( 'zotapay_payment_methods' );
			if ( empty( $payment_methods ) ) {
				return $gateways;
			}

			foreach ( $payment_methods as $payment_method ) {
				$gateways[] = new Zota_WooCommerce( $payment_method );
			}

			return $gateways;
		}
	);

	// Settings shortcut on plugins page.
	add_filter( 'plugin_action_links_zota-woocommerce/zota-woocommerce.php', 'wc_gateway_zota_settings_button', 10, 1 );

	// Add column OrderID on order list.
	add_filter( 'manage_edit-shop_order_columns', [ '\Zota\Zota_WooCommerce\Includes\Order', 'admin_columns' ], 10, 1 );
	add_action( 'manage_shop_order_posts_custom_column', [ '\Zota\Zota_WooCommerce\Includes\Order', 'admin_column_order_id' ], 10, 2 );

	// Scheduled check for pending payments.
	add_action( 'zota_scheduled_order_status', array( '\Zota\Zota_WooCommerce\Includes\Order', 'check_status' ), 10, 1 );
}

/**
 * Admin options scripts.
 *
 * @param  string $hook WooCommerce Hook.
 * @return void
 */
function zota_admin_enqueue_scripts( $hook ) {
	if ( 'woocommerce_page_wc-settings' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'zota-polyfill', ZOTA_WC_URL . '/dist/js/polyfill.js', array(), ZOTA_WC_VERSION, true );
	wp_enqueue_script( 'zota-woocommerce', ZOTA_WC_URL . '/dist/js/admin.js', array( 'jquery', 'zota-polyfill' ), ZOTA_WC_VERSION, true );

	$localization = array(
		'remove_payment_method_confirm' => esc_html__( 'Remove Payment Method?', 'zota-woocommerce' )
	);

	wp_localize_script(
		'zota-woocommerce',
		'zota',
		array(
			'localization' => $localization
		)
	);
}

/**
 * Add link to setings page in plugin list.
 *
 * @param array $links Array of plugin action links.
 * @return array
 */
function wc_gateway_zota_settings_button( $links ) {

	// Build the URL.
	$wc_gateway_zota_settings_page_url = add_query_arg(
		array(
			'page' => 'wc-settings',
			'tab' => 'checkout',
			'section' => 'wc_gateway_zota',
		),
		get_admin_url() . 'admin.php'
	);

	// Create the link.
	$wc_gateway_zota_settings_page_link = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $wc_gateway_zota_settings_page_url ),
		esc_html__( 'Settings', 'zota-woocommerce' )
	);

	// Add the link to the end of the array.
	array_push(
		$links,
		$wc_gateway_zota_settings_page_link
	);
	return $links;
}


/**
 * Deactivate plugin.
 *
 * @return void
 */
function wc_gateway_zota_deactivate() {

	// Remove any scheduled cron jobs or actions.
	wp_unschedule_hook( 'zota_scheduled_order_status' );

	if ( class_exists( 'ActionScheduler' ) ) {
		$actions = as_get_scheduled_actions(
			[
				'group' => ZOTA_WC_GATEWAY_ID,
				'status' => ActionScheduler_Store::STATUS_PENDING,
			],
			ARRAY_A
		);
		foreach ( $actions as $action ) {
			as_unschedule_all_actions( $action['hook'], $action['args'], ZOTA_WC_GATEWAY_ID );
		}
	}

	// Check requirements.
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
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
			Zotapay::getLogger()->debug(
				sprintf(
					// translators: %d is order ID.
					esc_html__( 'Order #%d not found.', 'zota-woocommerce' ),
					$order_id
				)
			);
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
