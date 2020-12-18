<?php
/**
 * Plugin Name: Zota for WooCommerce
 * Description: A plugin provides payment gateway for WooCommerce to Zota
 * Author: Zota Technology Ltd.
 * Author URI: https://zotapay.com/
 * Version: 1.0.2
 * Text Domain: zota-woocommerce
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.5.2
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ZotaWooCommerce
 * @author      Zota
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set constants.
define( 'ZOTA_WC_NAME', 'Zota for WooCommerce' );
define( 'ZOTA_WC_VERSION', '1.0.2' );
define( 'ZOTA_WC_GATEWAY_ID', 'wc_gateway_zota' );
define( 'ZOTA_WC_PLUGIN_ID', 'zotapay' );
define( 'ZOTA_WC_MIN_PHP_VER', '7.2.0' );
define( 'ZOTA_WC_MIN_WC_VER', '3.0' );
define( 'ZOTA_WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZOTA_WC_URL', plugins_url() . '/zota-woocommerce/' );

// Includes.
require_once ZOTA_WC_PATH . '/functions.php';
require_once ZOTA_WC_PATH . '/vendor/autoload.php';
require_once ZOTA_WC_PATH . '/includes/class-order.php';
require_once ZOTA_WC_PATH . '/includes/class-response.php';
require_once ZOTA_WC_PATH . '/includes/class-settings.php';

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
