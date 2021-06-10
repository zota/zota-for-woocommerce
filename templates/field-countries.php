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

$selections = (array) $value['value'];

$regions = include ZOTA_WC_PATH . 'i18n/countries.php';
$region_names = include ZOTA_WC_PATH . 'i18n/regions.php';

$field_id = preg_replace( '/[\[\]]+/', '-', $value['id'] );

?>
<tr valign="top">
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

				<?php if ( ! empty( $regions ) ) : ?>

					<ul>

						<?php foreach ( $regions as $key => $countries ) : ?>
							<li>
								<span>
									<?php
									printf(
										'<input type="checkbox" name="%1$s[]" value="%2$s" id="%1$s-%2$s" %4$s><label for="%1$s-%2$s">%3$s</label>',
										esc_attr( $value['id'] ),
										esc_attr( $key ),
										esc_html( $region_names[ $key ] ),
										in_array( $key, $selections, true ) ? 'checked' : ''
									);
									?>
								</span>
								<ul>

									<?php foreach ( $countries as $code => $country ) : ?>
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
