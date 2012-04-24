/**
 * Checks form fields to make sure they haven't changed before saving
 *
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @licence		http://www.opensource.org/licenses/mit-license.php
 */
jQuery.checksave = function(context) {
	var pageVals = {}

	var _this = this;
	
	var $elems = jQuery('input:text, input:checked, textarea, select', context);

	// get current values
	$elems.each(function(i){
		jQuery(this).data('startValue', jQuery(this).val());
	});

	window.onbeforeunload = function(e){
		var msg = null;
		var changedMsg = 'You are about to lose unsaved data. Do you want to continue?';
	    $elems.each(function(i){
			if (jQuery(this).data('startValue') && jQuery(this).data('startValue').toString() != jQuery(this).val().toString()){
				//console.log(jQuery(this).data('startValue').toString()  + ' --  ' + jQuery(this).val().toString() + ' -- ' + jQuery(this).attr('name'))
				msg = changedMsg;
				return changedMsg;
			}
		});
		return msg;
	}
};

jQuery.removeChecksave = function(){
	window.onbeforeunload = null;
};

jQuery.changeChecksaveValue = function(elem, val){
	jQuery(elem).data('startValue', val);
};

;(function($){
	jQuery.fn.checksave = function(o) {
		return this.each(function(){
		});
	};
})(jQuery);
	