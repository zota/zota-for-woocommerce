<?php
/**
 * Settings field icon
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
			<span class="woocommerce-help-tip" data-tip="<?php echo esc_html( $value['desc'] ); ?>"></span>
		</label>
	</th>
	<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
		<input
			type="hidden"
			id="<?php echo esc_attr( $value['id'] ); ?>"
			name="<?php echo esc_attr( $value['id'] ); ?>"
			value="<?php echo esc_attr( $value['value'] ); ?>"
			>
		<img
			src="<?php echo ! empty( $value['value'] ) ? esc_url( wp_get_attachment_image_url( $value['value'], 'medium' ) ) : ''; ?>"
			width="300"
			style="display:<?php echo ! empty( $value['value'] ) ? 'block' : 'none'; ?>"
			>
		<p class="controls">
			<button class="button-primary add-media">
				<?php esc_html_e( 'Add Logo', 'zota-woocommerce' ); ?>
			</button>
			<button class="button-secondary remove-media" style="display:<?php echo ! empty( $value['value'] ) ? 'inline-block' : 'none'; ?>">
				<?php esc_html_e( 'Remove Logo', 'zota-woocommerce' ); ?>
			</button>
		</p>
	</td>
</tr>
