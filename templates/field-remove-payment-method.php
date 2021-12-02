<?php
/**
 * Settings remove payment method.
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<tr valign="top">
	<th scope="row" class="titledesc">
	</th>
	<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
		<button
			id="remove-payment-method-<?php echo esc_attr( $value['id'] ); ?>"
			class="button remove-payment-method"
			data-id="<?php echo esc_attr( $value['id'] ); ?>"
			value="<?php esc_html_e( 'Remove Payment Method', 'zota-woocommerce' ); ?>"
			>
			<?php esc_html_e( 'Remove Payment Method', 'zota-woocommerce' ); ?>
		</button>
	</td>
</tr>
