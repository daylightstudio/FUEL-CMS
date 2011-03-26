// standard slugify borrowed from http://www.milesj.me/resources/snippet/13
function url_title(text) {
	text = text.replace(/[^-a-zA-Z0-9,&\s]+/ig, '');
	text = text.replace(/-/gi, "_");
	text = text.replace(/\s/gi, "-");
	text = text.toLowerCase();
	return text;
}

// // to match PHP strtolower function
function strtolower(text){
	return text.toLowerCase();	
}

// to match PHP strtoupper function
function strtoupper(text){
	return text.toUpperCase();
}
