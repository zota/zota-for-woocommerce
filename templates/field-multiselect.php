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

$countries = wc_gateway_zota_get_countries();
$regions   = wc_gateway_zota_get_regions();
$selected  = (array) $value['value'];

var_dump( $key );
var_dump( $value['placeholder'] );

?>
<tr valign="top" class="<?php echo esc_attr( $key ); ?>">
	<th scope="row" class="titledesc">
		<?php echo esc_html( $value['title'] ); ?>
	</th>
	<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
	<?php

		printf(
			'<select id="%1$s" name="%1$s[]" class="selectpicker select-%2$s" data-placeholder="%3$s&hellip;" aria-label="%4$s">',
			esc_attr( $value['id'] ),
			esc_attr( $key ),
			esc_attr( $value['placeholder'] ),
			esc_attr( $value['title'] )
		);

		if ( ! empty( $value['options'] ) ) {

			foreach ( $value['options'] as $option_slug => $option_name ) {

				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $option_slug ),
					in_array( $option_slug, $selected, true ) ? ' selected' : '',
					esc_html( $country )
				);

			}

		}

		echo '</select>';
	?>
	</td>
</tr>
