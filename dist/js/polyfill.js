/**
 * Polyfills.
 *
 * @package ZotaWooCommerce
 */

if ( ! Array.prototype.forEach ) {
	Array.prototype.forEach = function(callback) {
		var count = this.length;
		for (var i = 0; i < count; i++) {
			callback( this[i], i, this ) // currentValue, index, array.
		}
	}
}

if ( window.NodeList && ! NodeList.prototype.forEach ) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}
