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

$countries  = wc_gateway_zota_get_countries();
$regions    = wc_gateway_zota_get_regions();
$selections = (array) $value['value'];

?>
<tr valign="top" class="countries">
	<th scope="row" class="titledesc">
		<?php echo esc_html( $value['title'] ); ?>
	</th>
	<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
	<?php
		printf(
			'<select id="%1$s" name="%1$s[]" class="select-countries wc-enhanced-select" multiple="multiple" data-placeholder="%2$s&hellip;" aria-label="%3$s" >',
			esc_attr( $value['id'] ),
			esc_html__( 'Choose countries', 'zota-woocommerce' ),
			esc_html__( 'Country', 'zota-woocommerce' )
		);

		if ( ! empty( $countries ) ) {

			foreach ( $countries as $region_slug => $countries_by_region ) {

				printf(
					'<optgroup label="%s">',
					esc_attr( $regions[ $region_slug ] )
				);

				foreach ( $countries_by_region as $code => $country ) {
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						esc_attr( $code ),
						esc_html( $country ),
						in_array( $code, $selections, true ) ? ' selected' : ''
					);
				}

			 	echo '</optgroup>';

			}

		}

		echo '</select>';
	?>
	</td>
</tr>
