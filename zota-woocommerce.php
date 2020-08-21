<?php
/**
 * Plugin Name: Zota for WooCommerce
 * Description: A plugin provides payment gateway for WooCommerce to Zota
 * Author: Zota Technology Ltd.
 * Author URI: https://zotapay.com/
 * Version: 0.1.0
 * Text Domain: zota-woocommerce
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.0
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
define( 'ZOTA_WC_VERSION', '0.1.0' );
define( 'ZOTA_WC_GATEWAY_ID', 'wc_gateway_zota' );
define( 'ZOTA_WC_MIN_PHP_VER', '7.2.0' );
define( 'ZOTA_WC_MIN_WC_VER', '3.0' );
define( 'ZOTA_WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZOTA_WC_URL', plugins_url() . '/zota-woocommerce/' );

// Check if all requirements are ok.
$woocommerce_active  = in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
$woocommerce_version = version_compare( get_option( 'woocommerce_db_version' ), ZOTA_WC_MIN_WC_VER, '>=' );
$php_version         = version_compare( PHP_VERSION, ZOTA_WC_MIN_PHP_VER, '>=' );

if ( true === $woocommerce_active && true === $woocommerce_version && true === $php_version ) {
	// Initialize.
	add_action(
		'plugins_loaded',
		function() {
			// Load the textdomain.
			load_plugin_textdomain( 'zota-woocommerce', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

			// Includes.
			require_once ZOTA_WC_PATH . '/autoload.php';
			require_once ZOTA_WC_PATH . '/includes/class-logger.php';
			require_once ZOTA_WC_PATH . '/includes/class-settings.php';
			require_once ZOTA_WC_PATH . '/includes/class-zota-woocommerce.php';
			require_once ZOTA_WC_PATH . '/includes/class-zotapay-request.php';
			require_once ZOTA_WC_PATH . '/includes/class-zotapay-response.php';

			// Add to woocommerce payment gateways.
			add_filter(
				'woocommerce_payment_gateways',
				function ( $methods ) {
					$methods[] = 'Zota_WooCommerce';
					return $methods;
				}
			);
		}
	);
} else {
	deactivate_plugins( plugin_basename( __FILE__ ) );

	add_action(
		'admin_notices',
		function() {
			?>
			<div class="updated error">
				<p>
					<?php
					printf(
						wp_kses(
							// translators: %1$s Plugin name, %2$s PHP required version, %3$s WooCommerce required version.
							__( 'The plugin <strong>"%1$s"</strong> needs <strong>PHP version %2$s and WooCommerce version %3$s</strong> or newer.', 'zota-woocommerce' ),
							array(
								'strong' => array(),
							)
						),
						esc_html( ZOTA_WC_NAME ),
						esc_html( ZOTA_WC_MIN_PHP_VER ),
						esc_html( ZOTA_WC_MIN_WC_VER )
					);
					?>
					<br>
					<strong>
					<?php
					printf(
						// translators: %s Plugin name.
						esc_html__( '"%s" has been deactivated.', 'zota-woocommerce' ),
						esc_html( ZOTA_WC_NAME )
					);
					?>
					</strong>
				</p>
			</div>
			<?php
		}
	);
}

/**
 * Register activation hook.
 */
function zota_woocommerce_on_activate_callback() {
}

/**
 * Register deactivation hook.
 */
function zota_woocommerce_on_deactivate_callback() {
}
