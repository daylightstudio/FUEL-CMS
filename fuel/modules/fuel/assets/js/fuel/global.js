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
	if (!$('#' + modalId, $context).size()){
		var modalHTML = '<div id="' + modalId + '"><div class="loader"></div><a href="#" class="modal_close jqmClose"></a><div class="modal_content"></div></div>';
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
	$modal.attr('class', '__fuel__ __fuel_modal__ jqmWindow ' + cssClass)
	
	var modalWidth = $modal.outerWidth();
	var centerWidth = -((modalWidth/2));
	$modal.css('marginLeft', centerWidth + 'px');
	$modal.find('.modal_content').empty().append(html);
	$modal.find('iframe').load(function(){
		
		$('.jqmWindow .loader', $context).hide();
		var iframe = this;
		var contentDoc = iframe.contentDocument;

		$('.cancel, .modal_close', contentDoc).click(function(){
			$modal.jqmHide();
		})

		if (autoResize){
			setTimeout(function(){
					// if ($('#login', contentDoc).size()){
					// 	var docHeight = $('#login', contentDoc).outerHeight(); // bottom margin is added... not sure from what though
					// } else {
					// 	var docHeight = fuel.calcHeight('#fuel_main_top_panel, #fuel_actions, #fuel_notification, #fuel_main_content_inner, #list_container, .instructions', contentDoc) + 30;
					// }
					docHeight = fuel.calcHeight(contentDoc);
				//	docHeight = iframe.contentDocument.body.scrollHeight;
					//console.log(iframe.contentWindow.parent.document.title + ' ' + $(iframe.contentWindow.parent.document).height() )
					if (docHeight > 450) {
						docHeight = 450;
					}
					//console.log(iframe.contentWindow.document.title)

					//console.log($(iframe.contentWindow.parent.parent).find('iframe'))
					// set the height of the parent iframe if it needs to be bigger
					//if ($(iframe.contentWindow.parent.document).height() < docHeight){
						$(iframe.contentWindow.parent.document).find('iframe').height(docHeight)
				//	}
					fuel.cascadeIframeWindowSize(iframe.contentWindow, docHeight);
					//docHeight = docHeight - (fuel.windowLevel() * 50);
					$(iframe).height(docHeight);
			}, 250);
		}
		
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

fuel.calcHeight = function(context){
	var height = 0;
	if ($('#login', context).size()){
		var elems = '#login'; // bottom margin is added... not sure from what though
	} else {
		var elems = '#fuel_main_top_panel, #fuel_actions, #fuel_notification, #fuel_main_content_inner, #list_container, .instructions';
	}
	$(elems, context).each(function(i){
		height += $(this).outerHeight();
	})
	return height;
}


fuel.cascadeIframeWindowSize = function(win, height){
	var level = 0;
	//var win = window;
	// console.log(win.document.title)
	$('.inline_iframe', top.window.document).height(height + 100)
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