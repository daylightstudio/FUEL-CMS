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


fuel.modalWindow = function(html, cssClass, autoResize, onLoadCallback, onCloseCallback){

	var modalId = '__FUEL_modal__';
	if (!cssClass) cssClass = '';
	var $context = $('body', window.document);
	if (!$('#' + modalId, $context).length){
		var modalHTML = '<div id="' + modalId + '"><div class="loader"></div><a href="#" class="modal_close jqmClose"></a><div class="modal_content"></div></div>';
	} else {
		$('#' + modalId, $context).html('<div class="loader"></div><a href="#" class="modal_close jqmClose"></a><div class="modal_content"></div>');
	}
	

	
	var modalOnHide = function(){
		$('#' + modalId, $context).hide();
		$('.jqmOverlay', $context).remove();
		if (onCloseCallback) onCloseCallback();
	}	
	
	$context.append(modalHTML);
	$modal = $('#' + modalId, $context);
	$modal.attr('class', '__fuel__ __fuel_modal__ jqmWindow ' + cssClass)
	
	var modalWidth = $modal.outerWidth();
	var centerWidth = -((modalWidth/2));
	$modal.css('marginLeft', centerWidth + 'px');

	
	// show it first so we don't get the cancellation error in the console
	
	// set jqm window options
	var jqmOpts = { onHide: modalOnHide, toTop:true };
	if (onLoadCallback){
		jqmOpts.onLoad = onLoadCallback;
	}
	
	$modal.jqm(jqmOpts).jqmShow();
	$modal.find('.modal_content').empty().append(html);
	$modal.find('iframe').load(function(){
		$('.jqmWindow .loader', $context).hide();
		var iframe = this;
		var contentDoc = iframe.contentDocument;

		$('.cancel', contentDoc).add('.modal_close').click(function(e){
			e.preventDefault();
			$modal.jqmHide();
		})

		if (autoResize){
			setTimeout(function(){
					docHeight = fuel.calcHeight(contentDoc);
					$(iframe.contentWindow.parent.document).find('iframe').height(docHeight)
					fuel.cascadeIframeWindowSize(docHeight);
					$(iframe).height(docHeight);
			}, 250);
		}
		
	})
	
	return $modal;
}

fuel.closeModal = function(){
	var modalId = '__FUEL_modal__';
	$('#' + modalId).jqmHide();
}

fuel.getModule = function(context){
	if (window.fuel && window.fuel.module){
		return window.fuel.module;
	}
	if (context == undefined) context = null;
	var module = ($('.__fuel_module__', context).length) ? $('.__fuel_module__', context).val() : null;
	return module;
}

fuel.getModuleURI = function(context){
	if (context == undefined) context = null;
	var module = ($('.__fuel_module_uri__').length) ? $('.__fuel_module_uri__').val() : null;
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

fuel.calcHeight = function(context){
	var height = 0;
	if ($('#login', context).length){
		var elems = '#login'; 
	} else {
		var elems = '#fuel_main_top_panel, #fuel_actions, #fuel_notification, #fuel_main_content_inner, #list_container, .instructions';
	}
	$(elems, context).each(function(i){
		height += $(this).outerHeight();
	})
	if (height > 480) {
		height = 480;
	} else {
		height += 30;
	}
	return height;
}

fuel.adjustIframeWindowSize = function(){
	var iframe = $('.inline_iframe', top.window.document);
	if (iframe.length){
		iframe = iframe[0];
		var contentDoc = iframe.contentDocument;
		var height = fuel.calcHeight(contentDoc);

		var width = $('#fuel_main_content_inner .form', contentDoc).width() + 50;
		$(iframe).height(height);
		$(iframe).width(width);
	}
}

fuel.cascadeIframeWindowSize = function(height){
	var level = 0;
	if (height) height = height + 100;
	//var win = window;
	// console.log(win.document.title)
	$('.inline_iframe', top.window.document).height(height);
	
	// do 
	// {
	// 	level++;
	// 	//height = fuel.calcHeight(win.document);
	// 	console.log($('.inline_iframe', win.document))
	// 	$('.inline_iframe', win.document).height(height);
	// 	win = win.parent;
	// 	console.log(win.document.title)
	// 
	// } while (win != top && win.parent != null)
//	return level;
}