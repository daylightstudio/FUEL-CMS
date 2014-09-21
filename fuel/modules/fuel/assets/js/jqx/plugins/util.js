jQuery.fn.exists = function() {
	return (this.size() > 0);
};

jQuery.fn.setClass = function(cssClass) {
	var j = jQuery(this).attr('className', cssClass);
	return j;
};

jQuery.fn.isHidden = function() {
	if (jQuery(this).css('display') == 'none') return true;
	return false;
};


// static function
jQuery.include = function(file, type){
	if (!type) type = 'script'
	$.ajax({async:false, url: file, dataType: type, error: function(){
		var msg = new jqx.lib.Message('There was an error in loading the file' + file, 'error');
		msg.display();
	}});
};
