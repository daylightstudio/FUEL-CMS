function initFuelNamespace(){
	var f;
	if (window.fuel == undefined){
		if (top.window.fuel != undefined){
			f = top.window.fuel;
		} else {
			f = {};
		}
	} else {
		f = window.fuel;
	}
	return f;
}
//fuel = initFuelNamespace();
//console.log(fuel)
if (fuel == undefined){
	var fuel = {};
}

fuel.lang = function(key){
	return __FUEL_LOCALIZED__[key];
}

// used to get id values in case the form fields are namespaced
fuel.getFieldId = function(field, context){
	if (window.__FUEL_INLINE_EDITING != undefined){
		var val = $('.__fuel_module__', context).attr('id');
		var prefix = val.split('--')[0];
		return prefix + '--' + field;
	} else {
		return field;
	}
}

fuel.getModule = function(context){
	// inline editing
	if (window.__FUEL_INLINE_EDITING != undefined){
		return $('.__fuel_module__', context).val();
	} else {
		// jqx controller instance name is "page"
		return page.module;
	}
}


fuel.modalWindow = function(html, cssClass, onLoadCallback, onCloseCallback){
	var modalId = '__FUEL_modal__';
	if (!cssClass) cssClass = '';
	var $context = $('body', window.document);
	if (!$('#' + modalId, $context).size()){
		var modalHTML = '<div id="' + modalId + '" class="__fuel__ __fuel_modal__ jqmWindow ' + cssClass + '"><div class="loader"></div><a href="#" class="modal_close jqmClose"></a><div class="modal_content"></div></div>';
	} else {
		$('#' + modalId, $context).html('<div class="loader"></div><a href="#" class="modal_close jqmClose"></a><div class="modal_content"></div>');
	}
	
	
	var modalOnHide = function(){
		$('#' + modalId, $context).hide();
		$('.jqmOverlay', $context).hide();
		if (onCloseCallback) onCloseCallback();
	}	
	
	$context.append(modalHTML);
	
	$modal = $('#' + modalId, $context);
	
	var modalWidth = $modal.outerWidth();
	var centerWidth = -((modalWidth/2));
	$modal.css('marginLeft', centerWidth + 'px');
	$modal.find('.modal_content').empty().append(html);
	$modal.find('iframe').load(function(){

		$('.jqmWindow .loader', $context).hide();
		var iframe = this;
		var contentDoc = iframe.contentDocument;
		$('.cancel', contentDoc).click(function(){
			$modal.jqmHide();
		})
		setTimeout(function(){
			if ($('#login').size()){
				var docHeight = $('#login', contentDoc).outerHeight(); // bottom margin is added... not sure from what though
				//var docWidth = $('#login', contentDoc).outerWidth(); // 74 includes the 37 in padding on each side
			} else {
				var heightFudge = $('#fuel_notification', contentDoc).outerHeight() + 30; // padding for #fuel_main_content_inner is 15 top and 15 bottom
				var docHeight = $('#fuel_main_content_inner .form', contentDoc).outerHeight() + $('#fuel_actions', contentDoc).outerHeight() + heightFudge; // bottom margin is added... not sure from what though
				if ($('.instructions', contentDoc).size()) docHeight += $('.instructions', contentDoc).outerHeight() + 20;
				//var docWidth = $('#fuel_main_content_inner .form', contentDoc).outerWidth() + 74; // 74 includes the 37 in padding on each side
			}

			if (docHeight > 450) {
				docHeight = 450;
			}
			docHeight = docHeight - (fuel.windowLevel() * 50);
			$(iframe).height(docHeight);
		}, 200);
		
	})
	
	// set jqm window options
	var jqmOpts = { onHide: modalOnHide, toTop:true };
	
	if (onLoadCallback){
		jqmOpts.onLoad = onLoadCallback;
	}
	$modal.jqm(jqmOpts).jqmShow();
	return $modal;
}

fuel.closeModal = function(){
	var modalId = '__FUEL_modal__';
	$('#' + modalId).jqmHide();
}

fuel.getModule = function(context){
	if (context == undefined) context = window;
	var module = ($('.__fuel_module__', context).size()) ? $('.__fuel_module__', context).val() : null;
	return module;
}

fuel.getModuleURI = function(context){
	if (context == undefined) context = window;
	var module = ($('.__fuel_module_uri__', context).size()) ? $('.__fuel_module_uri__', context).val() : null;
	return module;
}

fuel.isTop = function(){
	return self == top;
}

fuel.windowLevel = function(){
	var level = 0;
	var win = window;
	while (win != top && win.parent != null){ 
		level++; 
		win = win.parent;
	}
	return level;
}