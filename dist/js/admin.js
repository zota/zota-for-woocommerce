"use strict";

// Live / test mode settings fields display.
function toggleTestFields()
{
	if ( document.getElementById( 'zotapay_testmode' ) === null ) {
		return;
	}

	let testSettings = document.getElementsByClassName( 'test-settings' );
	let liveSettings = document.getElementsByClassName( 'live-settings' );

	[].forEach.call(
		testSettings,
		function (el) {
			let row = el.parentElement.parentElement;
			if ( document.getElementById( 'zotapay_testmode' ).checked === true ) {
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
			if ( document.getElementById( 'zotapay_testmode' ).checked === true ) {
				row.style.display = 'none';
			} else {
				row.removeAttribute( 'style' );
			}
		}
	);
}
if ( document.getElementById( 'zotapay_testmode' ) !== null ) {
	toggleTestFields();
	document.getElementById( 'zotapay_testmode' ).addEventListener(
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
				deletePaymentMethodListener();
				if (document.getElementById( 'zotapay_testmode' ) !== null) {
					toggleTestFields();
				}
		    }
		);
	});
}

// Delete payment methods
function deletePaymentMethodListener() {
	let buttonsDeletePaymentMethod = document.getElementsByClassName( 'delete-payment-method' );

	[].forEach.call(
		buttonsDeletePaymentMethod,
		function (button) {
			button.addEventListener( 'click', function( e ) {
					e.preventDefault();

					// TODO add confirmation

					let paymentMethod = document.getElementById( button.dataset.paymentMethod );
					if ( paymentMethod !== null ) {
						paymentMethod.remove();
					}
				}
			);
		}
	);
}
deletePaymentMethodListener();
