"use strict";

var checkboxTestmode = document.getElementById( 'woocommerce_wc_gateway_zota_testmode' );

displaySettings( checkboxTestmode );

window.onload = function () {
	if (checkboxTestmode !== null) {
		checkboxTestmode.addEventListener(
			'change',
			function () {
				displaySettings( checkboxTestmode );
			},
			false
		);
	}
}

function displaySettings(checkbox)
{

	if (checkbox === null) {
		return;
	}

	var testSettings = document.getElementsByClassName( 'test-settings' );
	var liveSettings = document.getElementsByClassName( 'live-settings' );

	[].forEach.call(
		testSettings,
		function (el) {
			let row = el.parentElement.parentElement.parentElement;
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
			let row = el.parentElement.parentElement.parentElement;
			if (checkbox.checked === true) {
				row.style.display = 'none';
			} else {
				row.removeAttribute( 'style' );
			}
		}
	);

}
