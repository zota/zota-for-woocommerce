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
				addMediaListener();
				if (document.getElementById( 'zotapay_testmode' ) !== null) {
					toggleTestFields();
				}
		    }
		);
	});
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

						// add object ID in form
						if ( el.parentElement.querySelector( 'input' ) === null ) {
							el.parentElement.querySelector( 'input' ).value = selection.id;
						}

						// add image preview
						if ( el.parentElement.querySelector( 'img' ) !== null ) {
							el.parentElement.querySelector( 'img' ).src = selection.url;
							el.parentElement.querySelector( 'img' ).style.display = 'block';
						}

						// show remove button
						if ( el.parentElement.querySelector( '.remove-media' ) !== null ) {
							el.parentElement.querySelector( '.remove-media' ).style.display = 'inline-block';
						}
					}).open();
				} );
			}
		);
	}
}

(function( $ ) {

	// // on upload button click
	// $('body').on( 'click', '.image-upload', function(e){
	//
	// 	e.preventDefault();
	//
	// 	var button = $(this),
	// 	custom_uploader = wp.media({
	// 		title: 'Insert image',
	// 		library : {
	// 			// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
	// 			type : 'image'
	// 		},
	// 		button: {
	// 			text: 'Use this image' // button label text
	// 		},
	// 		multiple: false
	// 	}).on('select', function() { // it also has "open" and "close" events
	// 		var attachment = custom_uploader.state().get('selection').first().toJSON();
	// 		button.html('<img src="' + attachment.url + '">').next().val(attachment.id).next().show();
	// 	}).open();
	//
	// });

	// on remove button click
	$('body').on('click', '.image-remove', function(e){

		e.preventDefault();

		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.hide().prev().html('Upload image');
	});

})(jQuery);

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
