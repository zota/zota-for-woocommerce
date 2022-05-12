<?php
/**
 * Plugin Name: Zota for WooCommerce
 * Description: A plugin provides payment gateway for WooCommerce to Zota
 * Version: 1.2.3
 * Requires at least: 4.7
 * Requires PHP: 7.2
 * Author: Zota Technology Ltd.
 * Author URI: https://zotapay.com/
 * Text Domain: zota-woocommerce
 *
 * WC requires at least: 3.0
 * WC tested up to:  6.4.1
 *
 * License: Apache-2.0
 * License URI: https://github.com/zotapay/zota-woocommerce/blob/master/LICENSE
 *
 * @package     ZotaWooCommerce
 * @author      Zota
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set constants.
define( 'ZOTA_WC_NAME', 'Zota for WooCommerce' );
// Note: this gets replaced at runtime by Github Actions during release, keep the version '1.1.1'.
define( 'ZOTA_WC_VERSION', '1.1.1' );
define( 'ZOTA_WC_GATEWAY_ID', 'wc_gateway_zota' );
define( 'ZOTA_WC_PLUGIN_ID', 'zotapay' );
define( 'ZOTA_WC_MIN_PHP_VER', '7.2.0' );
define( 'ZOTA_WC_MIN_WC_VER', '3.0' );
define( 'ZOTA_WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZOTA_WC_URL', plugin_dir_url( __FILE__ ) );

// Includes.
require_once ZOTA_WC_PATH . 'functions.php';
require_once ZOTA_WC_PATH . 'vendor/autoload.php';
require_once ZOTA_WC_PATH . 'includes/class-order.php';
require_once ZOTA_WC_PATH . 'includes/class-response.php';
require_once ZOTA_WC_PATH . 'includes/class-settings.php';

// Check requirements.
if ( wc_gateway_zota_requirements() ) {
	add_action( 'init', 'zota_plugin_init' );
	add_action( 'woocommerce_loaded', 'wc_gateway_zota_init' );
} else {
	add_action( 'admin_notices', 'wc_gateway_zota_requirements_error' );
}

/**
 * Register deactivation hook.
 */
register_deactivation_hook( __FILE__, 'wc_gateway_zota_deactivate' );
