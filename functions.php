<?php
/**
 * Main functions
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

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
	// Check if all requirements are ok.
	$woocommerce_active  = class_exists( 'WooCommerce' );
	$woocommerce_version = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, ZOTA_WC_MIN_WC_VER, '>=' );
	$php_version         = version_compare( PHP_VERSION, ZOTA_WC_MIN_PHP_VER, '>=' );

	return $woocommerce_active && $woocommerce_version && $php_version;
}


/**
 * Requirements error message.
 *
 * @return void
 */
function wc_gateway_zota_requirements_error() {
	global $pagenow;

	if ( 'plugins.php' !== $pagenow ) {
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

	if ( 'plugins.php' !== $pagenow ) {
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
		return;
	}
}

/**
 * Gateway init.
 *
 * @return void
 */
function wc_gateway_zota_init() {

	// Register admin scripts.
	add_action( 'admin_enqueue_scripts', 'zota_admin_enqueue_scripts' );

	// Register scripts.
	add_action( 'wp_enqueue_scripts', 'zota_enqueue_scripts' );

	// Hook filters and actions.
	add_filter( 'woocommerce_countries', array( '\Zota\Zota_WooCommerce\Includes\Settings', 'woocommerce_countries' ) );
	add_filter( 'woocommerce_continents', array( '\Zota\Zota_WooCommerce\Includes\Settings', 'woocommerce_continents' ) );
	add_filter( 'woocommerce_register_shop_order_post_statuses', array( '\Zota\Zota_WooCommerce\Includes\Order', 'register_shop_order_post_statuses' ) );
	add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( '\Zota\Zota_WooCommerce\Includes\Order', 'valid_order_statuses_for_payment_complete' ) );
	add_filter( 'wc_order_statuses', array( '\Zota\Zota_WooCommerce\Includes\Order', 'order_statuses' ) );
	add_filter( 'woocommerce_settings_tabs_array', array( '\Zota\Zota_WooCommerce\Includes\Settings', 'settings_tab' ), 50 );
	add_action( 'woocommerce_settings_tabs_' . ZOTA_WC_PLUGIN_ID, array( '\Zota\Zota_WooCommerce\Includes\Settings', 'settings_tabs' ) );
	add_action( 'woocommerce_save_settings_' . ZOTA_WC_PLUGIN_ID, array( '\Zota\Zota_WooCommerce\Includes\Settings', 'save_settings' ), 10, 0 );
	add_action( 'woocommerce_admin_field_icon', array( '\Zota\Zota_WooCommerce\Includes\Settings', 'field_icon' ) );
	add_action( 'woocommerce_admin_field_remove_payment_method', array( '\Zota\Zota_WooCommerce\Includes\Settings', 'field_remove_payment_method' ) );
	add_action( 'wp_ajax_add_payment_method', array( '\Zota\Zota_WooCommerce\Includes\Settings', 'add_payment_method' ) );
	add_action( 'woocommerce_admin_order_totals_after_total', array( '\Zota\Zota_WooCommerce\Includes\Order', 'add_total_row' ) );

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
	add_filter( 'plugin_action_links_zota-for-woocommerce/zota-for-woocommerce.php', 'wc_gateway_zota_settings_button' );

	// Add column OrderID on order list.
	add_filter( 'manage_edit-shop_order_columns', array( '\Zota\Zota_WooCommerce\Includes\Order', 'admin_columns' ) );
	add_action( 'manage_shop_order_posts_custom_column', array( '\Zota\Zota_WooCommerce\Includes\Order', 'admin_column_order_id' ), 10, 2 );

	// Scheduled check for pending payments.
	add_action( 'zota_scheduled_order_status', array( '\Zota\Zota_WooCommerce\Includes\Order', 'check_status' ) );
	add_action( 'woocommerce_order_status_cancelled', array( '\Zota\Zota_WooCommerce\Includes\Order', 'delete_expiration_time' ) );
	add_action( 'woocommerce_order_status_cancelled', array( '\Zota\Zota_WooCommerce\Includes\Order', 'set_expired' ) );
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

	// WordPress.
	wp_enqueue_media();

	// ZotaPay.
	wp_enqueue_style( 'zota-woocommerce-admin', ZOTA_WC_URL . 'dist/css/admin.css', array(), ZOTA_WC_VERSION );

	wp_enqueue_script( 'zota-polyfill', ZOTA_WC_URL . 'dist/js/polyfill.js', array(), ZOTA_WC_VERSION, true );
	wp_enqueue_script( 'zota-woocommerce', ZOTA_WC_URL . 'dist/js/admin.js', array( 'wp-i18n', 'jquery', 'selectWoo', 'zota-polyfill' ), ZOTA_WC_VERSION, true );

	wp_localize_script(
		'zota-woocommerce',
		'zota',
		array(
			'countries'    => wc_gateway_zota_get_countries(),
		)
	);
}


/**
 * Public scripts.
 *
 * @return void
 */
function zota_enqueue_scripts() {
	if ( is_admin() ) {
		return;
	}

	wp_register_style( 'zota-woocommerce', ZOTA_WC_URL . 'dist/css/styles.css', array( 'woocommerce-inline' ), ZOTA_WC_VERSION );

	if ( is_checkout() ) {
		wp_enqueue_style( 'zota-woocommerce' );
	}
}


/**
 * Declare High-Performance Order Storage (HPOS) compatibility.
 *
 * @return void
 */
function zota_declare_wc_compatibility() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', ZOTA_WC_PATH . 'zota-for-woocommerce.php', true );
	}
}


/**
 * Get regions.
 *
 * @return array
 */
function wc_gateway_zota_get_regions() {
	$regions = include ZOTA_WC_PATH . 'i18n/regions.php';
	if ( empty( $regions ) ) {
		return array();
	}

	asort( $regions );

	return $regions;
}


/**
 * Get countries with regions.
 *
 * @return array
 */
function wc_gateway_zota_get_countries() {
	$wc_gateway_zota_countries = include ZOTA_WC_PATH . 'i18n/countries.php';
	if ( empty( $wc_gateway_zota_countries ) ) {
		return array();
	}
	return $wc_gateway_zota_countries;
}


/**
 * Prepare countries for dropdown.
 *
 * @return array
 */
function wc_gateway_zota_list_countries() {
	$wc_gateway_zota_countries = wc_gateway_zota_get_countries();
	if ( empty( $wc_gateway_zota_countries ) ) {
		return array();
	}

	$countries = array();
	foreach ( $wc_gateway_zota_countries as $region ) {
		foreach ( $region as $country_code => $country_name ) {
			$countries[ $country_code ] = $country_name;
		}
	}

	asort( $countries );

	return $countries;
}


/**
 * Get countries by region.
 *
 * @param string $region Region code.
 *
 * @return array
 */
function wc_gateway_zota_get_countries_by_region( $region = '' ) {
	$countries = wc_gateway_zota_get_countries();
	if ( empty( $countries[ $region ] ) || ! is_array( $countries[ $region ] ) ) {
		return array();
	}
	return $countries[ $region ];
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
			'tab'  => 'zotapay',
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
			array(
				'group'  => ZOTA_WC_GATEWAY_ID,
				'status' => ActionScheduler_Store::STATUS_PENDING,
			),
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
	$args   = array(
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
