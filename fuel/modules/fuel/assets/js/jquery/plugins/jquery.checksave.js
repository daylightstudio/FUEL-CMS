/**
 * Checks form fields to make sure they haven't changed before saving
 *
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @licence		http://www.opensource.org/licenses/mit-license.php
 */

jQuery.checksave = function() {
	window._pageVals = {};
	var _this = this;
	
	// get current values
	$('input:text, input:checked, textarea, select').each(function(i){
		var fieldName = $(this).attr('name') + i;  // add i to help with fields that have the same name
		$(this).attr('data-fieldref', fieldName ) ;
		if (window._pageVals){
			if (window._pageVals[fieldName]){
				if (typeof(window._pageVals[fieldName]) == 'string'){
					window._pageVals[fieldName] = new Array(window._pageVals[fieldName]);
				}
				window._pageVals[fieldName].push($(this).val());
			} else {
				window._pageVals[fieldName] = $(this).val();
			}
		}
		
	});
	window.onbeforeunload = $.checkSaveChange;
};

jQuery.removeChecksave = function(){
	window.onbeforeunload = null;
};

jQuery.changeChecksaveValue = function(inputKey, val){
	if (window._pageVals){
		for(n in window._pageVals){
			var regex = new RegExp('^' + inputKey + '\\d+');
			if (regex.test(n)){
				window._pageVals[n] = val;
			}
		}
	}
};

jQuery.checkSaveChange = function(){
	var msg;
	var changedMsg = 'You are about to lose unsaved data. Do you want to continue?';
    $('input:text, input:checked, textarea, select').each(function(i){
		var fieldName = $(this).attr('name') + i; // add i to help with fields that have the same name
		if (window._pageVals){
			if (typeof(window._pageVals[fieldName]) != 'string'){
				var cmp = new Array();
				var selector = 'input:text[data-fieldref="' + fieldName + '"],input:checked[data-fieldref="' + fieldName + '"],textarea[data-fieldref="' + fieldName + '"],select[data-fieldref="' + fieldName + '"]';
				$(selector).each(function(i){
					var val = $(this).val();
					if (val){
						cmp.push(val.toString());
					}
				});
				if (window._pageVals[fieldName] && cmp.toString() != window._pageVals[fieldName].toString()){
					// console.log(cmp)
					// console.log(fieldName + ' :  ' + window._pageVals[fieldName] + ' : ' + cmp.toString());
					msg = changedMsg;
					return false;
				}
			
			} else if (window._pageVals[fieldName] != null && window._pageVals[fieldName].toString() != $(this).val().toString()){
				msg = changedMsg;
				return false;
			}
		}
	});
	return msg;
};
