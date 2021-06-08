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

		</div>
	</td>
</tr>
