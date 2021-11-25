/**
 * Admin scripts.
 *
 * @package ZotaWooCommerce
 */

"use strict";

var __               = wp.i18n.__;
var testmode         = document.getElementById( 'zotapay_settings[testmode]' );
var addPaymentMethod = document.getElementById( 'add-payment-method' );
var paymentMethods   = document.getElementById( 'zotapay-payment-methods' );

toggleTestFields();
toggleCountrysAndRegions();

// Add event listener to testmode checkbox.
if ( testmode !== null ) {
	testmode.addEventListener(
		'change',
		function () {
			toggleTestFields();
		},
		false
	);
}

// Add event listeners to saved payment methods ( Suitable for Payment tab ).
document.querySelectorAll( '.add-media' ).forEach(
	function ( el ) {
		addMediaListener( el );
	}
);

document.querySelectorAll( '.remove-media' ).forEach(
	function ( el ) {
		removeMediaListener( el );
	}
);

document.querySelectorAll( '.remove-payment-method' ).forEach(
	function ( el ) {
		removePaymentMethodListener( el );
	}
);

document.querySelectorAll( '.routing' ).forEach(
	function ( el ) {
		addRoutingListener( el );
	}
);

// Add payment methods.
if ( addPaymentMethod !== null ) {
	addPaymentMethod.addEventListener(
		'click',
		function( e ) {
			e.preventDefault();

			// Check parent node.
			if ( paymentMethods === null ) {
				return;
			}

			jQuery.post(
				ajaxurl,
				{
					'action': 'add_payment_method'
				},
				function( response ) {
					paymentMethods.insertAdjacentHTML( 'beforeend', response );

					let method = paymentMethods.lastChild;

					// Add buttons event listeners.
					addMediaListener( method.querySelector( '.add-media' ) );
					addRoutingListener( method.querySelector( '.routing' ) );
					removeMediaListener( method.querySelector( '.remove-media' ) );
					removePaymentMethodListener( method.querySelector( '.remove-payment-method' ) );

					method.querySelectorAll( '.wc-enhanced-select' ).forEach(
						function ( el ) {
							if ( el.classList.contains( 'select-regions' ) ) {
								modifyCountriesByRegion( el, 'select' );
								modifyCountriesByRegion( el, 'unselect' );
							}

							jQuery( el ).selectWoo({
								allowClear: true
							});
						}
					);

					// Tooltips.
					jQuery( document.body ).trigger( 'init_tooltips' );

					// Toggle test / live settings.
					toggleTestFields();
					toggleCountrysAndRegions();
				}
			);
		}
	);
}

/**
 * Live / test mode settings fields display.
 */
function toggleTestFields() {

	var testSettings = document.querySelectorAll( '.test-settings' );
	var liveSettings = document.querySelectorAll( '.live-settings' );

	if ( testmode === null || testSettings.length === 0 || liveSettings.length === 0 ) {
		return;
	}

	testSettings.forEach(
		function ( el ) {
			let row = el.parentElement.parentElement;
			if ( testmode.checked === true ) {
				row.removeAttribute( 'style' );
			} else {
				row.style.display = 'none';
			}
		}
	);

	liveSettings.forEach(
		function ( el ) {
			let row = el.parentElement.parentElement;
			if ( testmode.checked === true ) {
				row.style.display = 'none';
			} else {
				row.removeAttribute( 'style' );
			}
		}
	);
}

/**
 * Toggle countries and regions.
 */
function toggleCountrysAndRegions() {
	document.querySelectorAll( '.routing' ).forEach(
		function ( el ) {
			toggleCountryAndRegion( el );
		}
	);
}

/**
 * Toggle country and region.
 *
 * @param {node} el - The node that listener will be attached to.
 */
function toggleCountryAndRegion( el ) {
	let rowRouting = el.closest( 'tr' );
	if ( null === rowRouting ) {
		return;
	}

	let rowRegions = rowRouting.nextSibling.nextSibling;
	if ( null !== rowRegions ) {
		if ( el.checked === true ) {
			rowRegions.removeAttribute( 'style' );
		} else {
			rowRegions.style.display = 'none';
		}
	}

	let rowCountries = rowRegions.nextSibling.nextSibling;
	if ( null !== rowCountries ) {
		if ( el.checked === true ) {
			rowCountries.removeAttribute( 'style' );
		} else {
			rowCountries.style.display = 'none';
		}
	}
}

/**
 * Toggle country and region.
 *
 * @param {node}   el - The node that listener will be attached to.
 * @param {string} action - The event name.
 */
function modifyCountriesByRegion( el, action ) {
	jQuery( el ).on('select2:' + action, function (e) {

		// Get the countries node.
		let rowRegions = el.closest( 'tr' );
		if ( null === rowRegions ) {
			return;
		}

		let rowCountries = rowRegions.nextSibling.nextSibling;
		if ( null === rowRegions ) {
			return;
		}

		let selectCountries = rowCountries.querySelector( '.select-countries' );
		if ( null === rowRegions ) {
			return;
		}

		// Get region countries.
		if ( zota.countries.length < 1 ) {
			return;
		}

		let regionCountries = zota.countries[ e.params.data.id ];
		if ( 'undefined' === regionCountries ) {
			return;
		}

		// Modify the selected countries.
		let selectedCountries = jQuery( selectCountries ).val();

		if ( 'select' === action ) {
			Object.keys( regionCountries ).forEach(
				function ( key ) {
					if ( false === selectedCountries.hasOwnProperty( key ) ) {
						selectedCountries.push( key );
					}
				}
			);
		}

		if ( 'unselect' === action ) {
		    let filteredCountries = selectedCountries.filter( function( value, index, arr ){
				return ! regionCountries.hasOwnProperty( value );
		    });

			selectedCountries = filteredCountries;
		}

		jQuery( selectCountries ).val( selectedCountries );
		jQuery( selectCountries ).trigger( 'change' );
	});
}

/**
 * Remove payment method.
 *
 * @param {node} button - The node that listener will be attached to.
 */
function removePaymentMethodListener( button = null ) {

	if ( button === null ) {
		return;
	}

	button.addEventListener(
		'click',
		function( e ) {
			e.preventDefault();

			if ( ! confirm( __( 'Remove Payment Method?', 'zota-woocommerce' ) ) ) {
				return;
			}

			// Remove payment method.
			if ( document.getElementById( button.dataset.id ) !== null ) {
				document.getElementById( button.dataset.id ).remove();
			}

			// Add removal field.
			if ( paymentMethods !== null ) {
				var input = document.createElement( 'input' );
				input.setAttribute( 'type', 'hidden' );
				input.setAttribute( 'name', 'zotapay_payment_methods[' + button.dataset.id + '][remove]' );
				input.setAttribute( 'value', '1' );
				paymentMethods.appendChild( input );
			}
		}
	);
}

/**
 * Add media.
 *
 * @param {node} button - The node that listener will be attached to.
 */
function addMediaListener( button = null ) {

	if ( button === null ) {
		return;
	}

	button.addEventListener(
		'click',
		function( e ) {
			e.preventDefault();

			var mediaLibrary = wp.media(
				{
					library : {
						type : 'image'
					},
					multiple: false
				}
			).on(
				'select',
				function() {

					// Get media object.
					var selection = mediaLibrary.state().get( 'selection' ).first().toJSON();

					// Get parent element.
					var section = e.target.parentElement.parentElement;

					// Add object ID in form.
					section.querySelector( 'input' ).value = selection.id;

					// Add image preview.
					section.querySelector( 'img' ).src           = selection.url;
					section.querySelector( 'img' ).style.display = 'block';

					// Show remove button.
					e.target.nextElementSibling.style.display = 'inline-block';
				}
			).open();
		}
	);
}

/**
 * Remove media
 *
 * @param {node} button - The node that listener will be attached to.
 */
function removeMediaListener( button = null ) {

	if ( button === null ) {
		return;
	}

	button.addEventListener(
		'click',
		function( e ) {
			e.preventDefault();

			// Get parent element.
			var section = e.target.parentElement.parentElement;

			// Remove object ID in form.
			section.querySelector( 'input' ).value = '';

			// Remove image preview.
			section.querySelector( 'img' ).src           = '';
			section.querySelector( 'img' ).style.display = 'none';

			// Show remove button.
			e.target.style.display = 'none';
		}
	);
}

/**
 * Add routing.
 *
 * @param {node} button - The node that listener will be attached to.
 */
function addRoutingListener( button = null ) {

	if ( button === null ) {
		return;
	}

	button.addEventListener(
		'click',
		function( e ) {
			toggleCountryAndRegion( e.target );
		}
	);
}


// Select countries by region.
document.querySelectorAll( '.select-regions' ).forEach(
	function ( el ) {
		modifyCountriesByRegion( el, 'select' );
		modifyCountriesByRegion( el, 'unselect' );
	}
);

// Select countries.
jQuery(document).ready( function(){
	jQuery( '.select-regions' ).selectWoo({
		allowClear: true
	});

	jQuery( '.select-countries' ).selectWoo({
		allowClear: true
	});
});
