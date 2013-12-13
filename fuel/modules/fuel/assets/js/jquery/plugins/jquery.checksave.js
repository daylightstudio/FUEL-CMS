/**
 * Checks form fields to make sure they haven't changed before saving
 *
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @licence		http://www.opensource.org/licenses/mit-license.php
 */
jQuery.checksave = function(context) {
	var pageVals = {}

	var _this = this;
	
	var $elems = jQuery('input:text, input:checked, textarea, select', context);

	// get current values
	$elems.each(function(i){
		jQuery(this).data('checksaveStartValue', jQuery(this).val());
	});
	
	//var oldChecksave = window.onbeforeunload;
	window.onbeforeunload = function(e){
		var msg = '';
		var changedMsg = 'You are about to lose unsaved data. Do you want to continue?';
	    $elems.each(function(i){
			//console.log(jQuery(this).attr('name') + " ------ " + escape(jQuery(this).data('checksaveStartValue').toString())  + " ------"  + escape(jQuery(this).val().toString()) )
			if (jQuery(this).data('checksaveStartValue') != undefined && jQuery(this).data('checksaveStartValue').toString() != jQuery(this).val().toString()){
				msg = changedMsg;
				return changedMsg;
			}
		});
		if (msg.length){
			return msg;	
		}
	}
};

jQuery.removeChecksave = function(){
	window.onbeforeunload = null;
};

jQuery.removeChecksaveValue = function(elem){
	jQuery(elem).data('checksaveStartValue', null);
};

jQuery.changeChecksaveValue = function(elem, val){
	jQuery(elem).data('checksaveStartValue', val);
};

jQuery.refreshChecksaveValue = function(elem){
	var val = jQuery(elem).val();
	jQuery.changeChecksaveValue(elem, val);
};

;(function($){
	jQuery.fn.checksave = function(o) {
		return this.each(function(){
		});
	};
})(jQuery);
	