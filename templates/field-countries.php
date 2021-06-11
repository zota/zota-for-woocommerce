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
$field_id   = preg_replace( '/[\[\]]+/', '-', $value['id'] );

?>
<tr valign="top" class="countries">
	<th scope="row" class="titledesc">
		<?php echo esc_html( $value['title'] ); ?>
	</th>
	<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">

		<fieldset class="multiselect-dropdown" tabindex="-1">
			<legend class="screen-reader-text"><?php echo esc_html( $value['title'] ); ?></legend>

			<button type="button" class="button button-secondary" aria-expanded="false" aria-controls="<?php echo esc_attr( sprintf( '%s-dropdown', $field_id ) ); ?>" aria-label="<?php esc_attr_e( 'Choose countries / regions&hellip;', 'woocommerce' ); ?>">

				<span><?php esc_attr_e( 'Choose countries / regions&hellip;', 'woocommerce' ); ?></span>
				<span class="dropdown-arrow" role="presentation"><b role="presentation"></b></span>
			</button>
			<div id="<?php echo esc_attr( sprintf( '%s-dropdown', $field_id ) ); ?>">

				<?php if ( ! empty( $countries ) ) : ?>

					<ul>

						<?php foreach ( $countries as $region_slug => $countries_by_region ) : ?>
							<li>
								<span>
									<?php
									printf(
										'<input type="checkbox" name="%1$s[]" value="%2$s" id="%1$s-%2$s" %4$s><label for="%1$s-%2$s">%3$s</label>',
										esc_attr( $value['id'] ),
										esc_attr( $region_slug ),
										esc_html( $regions[ $region_slug ] ),
										in_array( $region_slug, $selections, true ) ? 'checked' : ''
									);
									?>
								</span>
								<ul>

									<?php foreach ( $countries_by_region as $code => $country ) : ?>
										<li>
											<span>
												<?php
												printf(
													'<input type="checkbox" name="%1$s[]" value="%2$s" id="%1$s-%2$s" %4$s><label for="%1$s-%2$s">%3$s</label>',
													esc_attr( $value['id'] ),
													esc_attr( $code ),
													esc_html( $country ),
													in_array( $code, $selections, true ) ? 'checked' : ''
												);
												?>
											</span>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>

					</ul>
				<?php endif; ?>

			</div>
		</fieldset>

	</td>
</tr>
