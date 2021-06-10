document.addEventListener( 'DOMContentLoaded', function() {

	var dropdowns = document.querySelectorAll( '.multiselect-dropdown' );
	console.log(dropdowns);
  	if ( dropdowns.lengh == 0 ) {
		return;
	}

	dropdowns.forEach( function( dropdown ) {
		let expandButton = dropdown.querySelector( 'button[aria-expanded]' );
		let menu = document.getElementById( expandButton.getAttribute( 'aria-controls' ) );
		let expanded = ( expandButton.getAttribute( 'aria-expanded' ) === 'true' );
		let checkboxes = menu.querySelectorAll( 'input' );

		let closeDropdown = function() {
			menu.hidden = true;
			expandButton.setAttribute( 'aria-expanded', false );
		};

		let toggleDropdown = function() {
			let expanded = ( expandButton.getAttribute( 'aria-expanded' ) === 'true' );
			menu.hidden = expanded;
			expandButton.setAttribute( 'aria-expanded', ! expanded );
		};

		let focusDown = function() {
			let current = document.activeElement;
			if ( current.closest( '.multiselect-dropdown' ) !== dropdown  ) {
				checkboxes.item( 0 ).focus();
			} else {
				i = [].indexOf.call( checkboxes, current );
				if ( i === checkboxes.length - 1 ) {
					return;
				}
				checkboxes.item( i + 1 ).focus();
			}
		}

		let focusUp = function() {
			let current = document.activeElement;
			if ( current.closest( '.multiselect-dropdown' ) !== dropdown  ) {
				checkboxes.item( checkboxes.length - 1 ).focus();
			} else {
				i = [].indexOf.call( checkboxes, current );
				if ( i === 0 ) {
					return;
				}
				checkboxes.item( i - 1 ).focus();
			}
		}

		let setButtonLabel = function() {
			let checked_boxes = menu.querySelectorAll( 'input:checked:not(:disabled)' );
			if ( checked_boxes.length > 0 ) {
				let label = dropdown.querySelector('label[for="' + checked_boxes.item( 0 ).id + '"]').innerHTML;
				label = label.replace( /\(\d*\)/, '' );
				if ( checked_boxes.length > 1 ) {
					label += ' + ' + ( checked_boxes.length - 1 ) + ' more';
				}
				expandButton.firstElementChild.innerHTML = label;
			} else {
				expandButton.firstElementChild.innerHTML = expandButton.dataset.label;
			}
		}

		if ( menu ) {
			menu.hidden = ! expanded;
		}

		if ( expandButton ) {
			expandButton.setAttribute( 'data-label', expandButton.firstElementChild.innerHTML );
			// So that we overwrite any other event handlers.
			expandButton.onclick = function( e ) {
				toggleDropdown();
			}
		}

		setButtonLabel();

		var focusTimeout;

		dropdown.addEventListener( 'focusout', function( e ) {
			focusTimeout = setTimeout( function() {
				if ( document.activeElement.closest( '.multiselect-dropdown' ) === dropdown ) {
					return;
				}
				closeDropdown();
			}, 200 );
		} );

		dropdown.addEventListener( 'focusin', function( e ) {
			clearTimeout( focusTimeout );
		} );

		// Fix Safari.
		dropdown.addEventListener( 'mouseup', function( e ) {
			this.focus();
			e.preventDefault();
		} );

		dropdown.addEventListener( 'keydown', function( e ) {
			switch ( e.keyCode ) {
				case 27:
					closeDropdown();
					expandButton.focus();
					break;
				case 40:
					e.preventDefault();
					focusDown();
					break;
				case 38:
					e.preventDefault();
					focusUp();
					break;
			}
		}, true );

		dropdown.addEventListener( 'change', function( e ) {
			setButtonLabel();
		} );

		checkboxes.forEach( function( checkbox ) {
			checkbox.addEventListener( 'change', function( e ) {
				let checked = this.checked;
				let children = this.closest( 'li' ).querySelectorAll( 'input' );
				children.forEach( function( child ) {
					child.checked = checked && ! child.disabled;
				} );
			} );
		} );

	} );

} );

if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

var ElementPrototype = window.Element.prototype;
if (typeof ElementPrototype.matches !== 'function') {
  ElementPrototype.matches = ElementPrototype.msMatchesSelector || ElementPrototype.mozMatchesSelector || ElementPrototype.webkitMatchesSelector || function matches(selector) {
    var element = this;
    var elements = (element.document || element.ownerDocument).querySelectorAll(selector);
    var index = 0;
    while (elements[index] && elements[index] !== element) {
      ++index;
    }
    return Boolean(elements[index]);
  };
}

if (typeof ElementPrototype.closest !== 'function') {
  ElementPrototype.closest = function closest(selector) {
    var element = this;
    while (element && element.nodeType === 1) {
      if (element.matches(selector)) {
        return element;
      }
      element = element.parentNode;
    }
    return null;
  };
}
