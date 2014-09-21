// requires the jquery.cookie.js, and the jquery.serialize.js

jQuery.supercookie = function(name, key, value, options) {
	if (typeof value != 'undefined') { // name and value given, set cookie
		var cookieObj = jQuery.supercookie(name);
		cookieObj[key] = value;
		var cookieVal = escape(jQuery.serialize(cookieObj));
		jQuery.cookie(name, cookieVal, options);
	} else { // only name given, get cookie
		var cookie = jQuery.cookie(name);
		var cookieObj = null;
		if (cookie) {
			eval('cookieObj = ' + unescape(cookie));
		}
		if (!cookieObj) cookieObj = {};
		if (key){
			if (cookieObj[key]){
				return cookieObj[key];
			} else {
				return null;
			}
		} else {
			return cookieObj;
		}
		
	}
};