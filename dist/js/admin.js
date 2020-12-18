"use strict";

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
