<?php
/**
 * Settings field countries
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wc_countries = new WC_Countries();
echo '<pre>';
wc_print_r( $wc_countries->get_countries() );
echo '</pre>';
// wc_print_r( wc_gateway_zota_get_countries() );

?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $value['id'] ); ?>">
			<?php echo esc_html( $value['title'] ); ?>
		</label>
	</th>
	<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
		<input
			type="hidden"
			id="<?php echo esc_attr( $value['id'] ); ?>"
			name="<?php echo esc_attr( $value['id'] ); ?>"
			value="<?php echo esc_attr( $value['value'] ); ?>"
			>
		<div class="checkbox-list">
			<label for="woocommerce_enable_myaccount_registration">
				<input name="woocommerce_enable_myaccount_registration" id="woocommerce_enable_myaccount_registration" type="checkbox" class="" value="1" checked="checked">
				Позволяване на клиентите да създадат профил на страницата "Моят профил"
			</label>
		</div>
	</td>
</tr>
