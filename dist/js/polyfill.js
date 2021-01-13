// Polyfill forEach.
if ( ! Array.prototype.forEach ) {
    Array.prototype.forEach = function(callback) {
        for (var i = 0; i < this.length; i++) {
            callback( this[i], i, this ) // currentValue, index, array
        }
    }
}

if ( window.NodeList && ! NodeList.prototype.forEach ) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}
