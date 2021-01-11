"use strict";

// Live / test mode settings fields display.
function toggleTestFields() {

	if ( document.getElementById( 'zotapay_settings[testmode]' ) === null ) {
		return;
	}

	let testSettings = document.getElementsByClassName( 'test-settings' );
	let liveSettings = document.getElementsByClassName( 'live-settings' );

	[].forEach.call(
		testSettings,
		function (el) {
			let row = el.parentElement.parentElement;
			if ( document.getElementById( 'zotapay_settings[testmode]' ).checked === true ) {
				row.removeAttribute( 'style' );
			} else {
				row.style.display = 'none';
			}
		}
	);

	[].forEach.call(
		liveSettings,
		function (el) {
			let row = el.parentElement.parentElement;
			if ( document.getElementById( 'zotapay_settings[testmode]' ).checked === true ) {
				row.style.display = 'none';
			} else {
				row.removeAttribute( 'style' );
			}
		}
	);
}

if ( document.getElementById( 'zotapay_settings[testmode]' ) !== null ) {
	toggleTestFields();
	document.getElementById( 'zotapay_settings[testmode]' ).addEventListener(
		'change',
		function () {
			toggleTestFields();
		},
		false
	);
}

// Add payment methods
var buttonAddPaymentMethod = document.querySelector( '#add-payment-method' );
if ( buttonAddPaymentMethod !== null ) {
	buttonAddPaymentMethod.addEventListener( 'click', function( e ) {
		e.preventDefault();

		// Get setction.
		var section = document.querySelector( '#zotapay-payment-methods' );
		if ( section === null ) {
			return;
		}

		jQuery.post(
	    	ajaxurl,
		    {
		        'action': 'add_payment_method',
		    },
		    function( response ) {
				section.insertAdjacentHTML( 'beforeend', response );

				// Add buttons event listeners
				removePaymentMethodListener();
				addMediaListener();
				removeMediaListener();

				// Tooltips
				jQuery( document.body ).trigger( 'init_tooltips' );

				if (document.getElementById( 'zotapay_settings[testmode]' ) !== null) {
					toggleTestFields();
				}
		    }
		);
	});
}

// Remove payment methods
function removePaymentMethodListener() {
	let buttonsDeletePaymentMethod = document.getElementsByClassName( 'remove-payment-method' );

	[].forEach.call(
		buttonsDeletePaymentMethod,
		function (button) {
			button.addEventListener( 'click', function( e ) {
					e.preventDefault();

					if ( confirm( zota.localization.remove_payment_method_confirm ) ) {
						let paymentMethod = document.getElementById( button.dataset.id );
						if ( paymentMethod !== null ) {
							paymentMethod.remove();
						}
					}


				}
			);
		}
	);
}

// Add media
function addMediaListener() {
	var buttonsAddMedia = document.querySelectorAll( '.add-media' );
	if ( buttonsAddMedia !== null ) {
		[].forEach.call(
			buttonsAddMedia,
			function (el) {
				el.addEventListener( 'click', function( e ) {
					e.preventDefault();

					var mediaLibrary = wp.media({
						library : {
							type : 'image'
						},
						multiple: false
					}).on( 'select', function() {
						// get media object
						var selection = mediaLibrary.state().get( 'selection' ).first().toJSON();

						// get parent element
						var section = el.parentElement.parentElement;

						// add object ID in form
						section.querySelector( 'input' ).value = selection.id;

						// add image preview
						section.querySelector( 'img' ).src = selection.url;
						section.querySelector( 'img' ).style.display = 'block';

						// show remove button
						el.nextElementSibling.style.display = 'inline-block';
					}).open();
				} );
			}
		);
	}
}

// Remove media
function removeMediaListener() {
	var buttonsRemoveMedia = document.querySelectorAll( '.remove-media' );
	if ( buttonsRemoveMedia !== null ) {
		[].forEach.call(
			buttonsRemoveMedia,
			function (el) {
				el.addEventListener( 'click', function( e ) {
					e.preventDefault();

					// get parent element
					var section = el.parentElement.parentElement;

					// remove object ID in form
					section.querySelector( 'input' ).value = '';

					// remove image preview
					section.querySelector( 'img' ).src = '';
					section.querySelector( 'img' ).style.display = 'none';

					// show remove button
					el.style.display = 'none';
				} );
			}
		);
	}
}

addMediaListener();
removeMediaListener();
removePaymentMethodListener();
