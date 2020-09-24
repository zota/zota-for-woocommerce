<?php
/**
 * Plugin Name: Zota for WooCommerce
 * Description: A plugin provides payment gateway for WooCommerce to Zota
 * Author: Zota Technology Ltd.
 * Author URI: https://zotapay.com/
 * Version: 1.0
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
define( 'ZOTA_WC_VERSION', '1.0' );
define( 'ZOTA_WC_GATEWAY_ID', 'wc_gateway_zota' );
define( 'ZOTA_WC_MIN_PHP_VER', '7.2.0' );
define( 'ZOTA_WC_MIN_WC_VER', '3.0' );
define( 'ZOTA_WC_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZOTA_WC_URL', plugins_url() . '/zota-woocommerce/' );

// Includes.
require_once ZOTA_WC_PATH . 'vendor/autoload.php';
require_once ZOTA_WC_PATH . '/includes/class-activator.php';
require_once ZOTA_WC_PATH . '/includes/class-order.php';
require_once ZOTA_WC_PATH . '/includes/class-response.php';
require_once ZOTA_WC_PATH . '/includes/class-settings.php';

// Check requirements
function zota_woocommerce_requirements() {

	if ( ! \Zota\Zota_WooCommerce\Includes\Activator::requirements() ) {

		if( is_plugin_active( plugin_basename( __FILE__ )) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		add_action(
			'admin_notices',
			function() {
				?>
				<div class="updated error">
					<p>
						<strong><?php echo esc_html( ZOTA_WC_NAME ); ?></strong>
						<?php
						printf(
							// translators: %1$s PHP version, %2$s WooCommerce version.
							esc_html__( ' needs PHP version %1$s and WooCommerce version %2$s or newer.', 'zota-woocommerce' ),
							esc_html( ZOTA_WC_MIN_PHP_VER ),
							esc_html( ZOTA_WC_MIN_WC_VER )
						);
						?>
						<br>
						<strong>
						<?php
						printf(
							// translators: %s Plugin name.
							esc_html__( '%s has been deactivated.', 'zota-woocommerce' ),
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
}
add_action( 'admin_init', 'zota_woocommerce_requirements' );

/**
 * Register activation hook.
 */
register_activation_hook( __FILE__, array( '\Zota\Zota_WooCommerce\Includes\Activator', 'activate' ) );

/**
 * Register deactivation hook.
 */
register_deactivation_hook( __FILE__, array( '\Zota\Zota_WooCommerce\Includes\Activator', 'deactivate' ) );
