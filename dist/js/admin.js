"use strict";

// Live / test mode settings fields display.
var checkboxTestmode = document.getElementById( 'zotapay_testmode' );

if (checkboxTestmode !== null) {
	displaySettings( checkboxTestmode );

	checkboxTestmode.addEventListener(
		'change',
		function () {
			displaySettings( checkboxTestmode );
		},
		false
	);
}

function displaySettings(checkbox)
{
	if (checkbox === null) {
		return;
	}

	let testSettings = document.getElementsByClassName( 'test-settings' );
	let liveSettings = document.getElementsByClassName( 'live-settings' );

	[].forEach.call(
		testSettings,
		function (el) {
			let row = el.parentElement.parentElement;
			if (checkbox.checked === true) {
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
			if (checkbox.checked === true) {
				row.style.display = 'none';
			} else {
				row.removeAttribute( 'style' );
			}
		}
	);

}

// Add payment methods
document.querySelector( '#add-payment-method' ).addEventListener( 'click', function( e ) {
	e.preventDefault();

	// Get setction description tag.
	var title = document.querySelector( '#zotapay-section-payment-methods-description' );
	if ( title === null ) {
		return;
	}

	// Get payment methods table.
	var table = title.nextSibling;

	// TODO fix table's child element and append to it
	// console.log( table.firstChild );
	// Check if table tag is empty or has tbody
	var appendTo = table.firstChild === 'undefined' ? table : table.firstChild;

	jQuery.post(
    	ajaxurl,
	    {
	        'action': 'add_payment_method',
	    },
	    function( response ) {
			table.innerHTML += response + '<tr><td colspan="2"><hr></td>';

			if (checkboxTestmode !== null) {
				displaySettings( checkboxTestmode );
			}
	    }
	);
});
