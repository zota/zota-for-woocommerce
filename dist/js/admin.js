"use strict";

var testmode = document.getElementById( 'zotapay_settings[testmode]' );

toggleTestFields();

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

// Add event listeners to saved payment methods.
if ( document.querySelectorAll( '.payment_method' ).length !== 0 ) {
	document.querySelectorAll( '.payment_method' ).forEach(
		function ( method ) {
			addMediaListener( method.querySelector( '.add-media' ) );
			removeMediaListener( method.querySelector( '.remove-media' ) );
			removePaymentMethodListener( method.querySelector( '.remove-payment-method' ) );
		}
	);
}

// Add payment methods.
if ( document.getElementById( 'add-payment-method' ) !== null ) {
	document.getElementById( 'add-payment-method' ).addEventListener( 'click', function( e ) {
		e.preventDefault();

		// Check parent node.
		if ( document.getElementById( 'zotapay-payment-methods' ) === null ) {
			return;
		}

		jQuery.post(
	    	ajaxurl,
		    {
		        'action': 'add_payment_method',
		    },
		    function( response ) {
				document.getElementById( 'zotapay-payment-methods' ).insertAdjacentHTML( 'beforeend', response );

				let method = document.getElementById( 'zotapay-payment-methods' ).lastChild;

				// Add buttons event listeners.
				addMediaListener( method.querySelector( '.add-media' ) );
				removeMediaListener( method.querySelector( '.remove-media' ) );
				removePaymentMethodListener( method.querySelector( '.remove-payment-method' ) );

				// Tooltips.
				jQuery( document.body ).trigger( 'init_tooltips' );

				// Toggle test / live settings.
				toggleTestFields();
		    }
		);
	});
}

/**
 * Live / test mode settings fields display.
 */
function toggleTestFields() {

	if ( testmode === null
		|| document.querySelectorAll( '.test-settings' ).length === 0
	 	|| document.querySelectorAll( '.live-settings' ).length === 0 ) {
		return;
	}

	document.querySelectorAll( '.test-settings' ).forEach(
		function ( el ) {
			let row = el.parentElement.parentElement;
			if ( testmode.checked === true ) {
				row.removeAttribute( 'style' );
			} else {
				row.style.display = 'none';
			}
		}
	);

	document.querySelectorAll( '.live-settings' ).forEach(
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
 * Remove payment method.
 *
 * @param {node} button - The node that listener will be attached to.
 */
function removePaymentMethodListener( button = null ) {

	if ( button === null ) {
		return;
	}

	button.addEventListener( 'click', function( e ) {
			e.preventDefault();

			if ( ! confirm( zota.localization.remove_payment_method_confirm ) ) {
				return;
			}

			// Remove payment method.
			if ( document.getElementById( button.dataset.id ) !== null ) {
				document.getElementById( button.dataset.id ).remove();
			}

			// Add removal field.
			if ( document.getElementById( 'zotapay-payment-methods' ) !== null ) {
				var input = document.createElement( 'input' );
				input.setAttribute( 'type', 'hidden' );
				input.setAttribute( 'name', 'zotapay_payment_methods[' + button.dataset.id + '][remove]' );
				input.setAttribute( 'value', '1' );
				document.getElementById( 'zotapay-payment-methods' ).appendChild( input );
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

	button.addEventListener( 'click', function( e ) {
		e.preventDefault();

		var mediaLibrary = wp.media({
			library : {
				type : 'image'
			},
			multiple: false
		}).on( 'select', function() {

			// Get media object.
			var selection = mediaLibrary.state().get( 'selection' ).first().toJSON();

			// Get parent element.
			var section = e.target.parentElement.parentElement;

			// Add object ID in form.
			section.querySelector( 'input' ).value = selection.id;

			// Add image preview.
			section.querySelector( 'img' ).src = selection.url;
			section.querySelector( 'img' ).style.display = 'block';

			// Show remove button.
			e.target.nextElementSibling.style.display = 'inline-block';
		}).open();
	});
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

	button.addEventListener( 'click', function( e ) {
		e.preventDefault();

		// Get parent element.
		var section = e.target.parentElement.parentElement;

		// Remove object ID in form.
		section.querySelector( 'input' ).value = '';

		// Remove image preview.
		section.querySelector( 'img' ).src = '';
		section.querySelector( 'img' ).style.display = 'none';

		// Show remove button.
		e.target.style.display = 'none';
	});
}
