// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2007 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------

function markitupLanguage(key)
{
	if (window['jqx'] != undefined)
	{
		return jqx.config.localized['markitup_' + key];
	}
	else if (window['__FUEL_LOCALIZED__'] != undefined)
	{
		return __FUEL_LOCALIZED__['markitup_' + key];
	}
}

myMarkItUpSettings = {
	root: 'skins/simple/',
	nameSpace:           "html", // Useful to prevent multi-instances CSS conflict
    previewParserPath:   __FUEL_PATH__ + "/preview",
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [ 	
		{name:markitupLanguage('b'), key:'B', className:'bold', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
		{name:markitupLanguage('i'), key:'I', className:'italic', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
		{name:markitupLanguage('strike'), className:'stroke', key:'S', openWith:'<del>', closeWith:'</del>' },
		{separator:'---------------' },
    	{name:markitupLanguage('p'), className:'p', key:'P', openWith:'<p>', closeWith:'</p>'}, 
        {name:markitupLanguage('h1'), className:'h1', key:'1', openWith:'<h1>', closeWith:'</h1>', placeHolder:markitupLanguage('placeholder_headings') },
        {name:markitupLanguage('h2'), className:'h2', key:'2', openWith:'<h2>', closeWith:'</h2>', placeHolder:markitupLanguage('placeholder_headings') },
        {name:markitupLanguage('h3'), className:'h3',key:'3', openWith:'<h3>', closeWith:'</h3>', placeHolder:markitupLanguage('placeholder_headings') },
        {name:markitupLanguage('h4'), className:'h4', key:'4', openWith:'<h4>', closeWith:'</h4>', placeHolder:markitupLanguage('placeholder_headings') },
        {separator:'---------------' },
		{name:markitupLanguage('ol'), className:'ol', key:'', openWith:'<ol>', closeWith:'</ol>' },
		{name:markitupLanguage('ul'), className:'ul', key:'', openWith:'<ul>', closeWith:'</ul>'  },
		{name:markitupLanguage('li'), className:'li', key:'', openWith:'<li>', closeWith:'</li>' },
		{name:markitupLanguage('blockquote'), className:'blockquote', key:'', openWith:'<blockquote>', closeWith:'</blockquote>' },
		{name:markitupLanguage('hr'), className:'hr', key:'', openWith:'<hr />'},
		{separator:'---------------' },
		/*{name:'Image', className:'image', key:'I', replaceWith:'<img src="{img_path}\[![Source:!:]!]\" alt="[![Alternative text]!]" />' },*/
		{name:markitupLanguage('img'), className: 'image', key: 'L', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpImageInsert(marketItup); 
				return false;
			}
		},
		{name:markitupLanguage('link'), className:'link', key:'L', openWith:'<a href="{site_url(\'[![' + markitupLanguage('link') + ':!:]!]\')}" target="[![' + markitupLanguage('target') + ':!:_self]!]">', closeWith:'</a>', placeHolder:markitupLanguage('placeholder_link')},
		
		{name:markitupLanguage('mailto'), className:'mailto', key:'M', openWith:'{safe_mailto(', closeWith:')}', placeHolder:markitupLanguage('placeholder_email') },
		{name:markitupLanguage('php'), className:'fuel_var', key:'', openWith:'{$[![' + markitupLanguage('php') + ':!:]!]', closeWith:'}', placeHolder:'' },
		{separator:'---------------' },
		{name:markitupLanguage('clean'), className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },		
		{name:markitupLanguage('preview'), className:'preview',  call:'preview'},
		{name: markitupLanguage('fullscreen'), className: 'maximize', key: 'F', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpFullScreen(marketItup); 
				return false;
			}
		},
	]
}

myMarkItUpSettings.markItUpFullScreen = function (markItUp){
	
	var origTextarea = jQuery(markItUp.textarea);
	var val = origTextarea.val();
	var textarea = jQuery('#fullscreen');
	var container = textarea.parents('.markItUp');
	
	var minimize = function(){
		origTextarea.val(textarea.val());
		textarea.unbind();
		
		// TODO: fix markitup selection error
		container.jqmHide();
		container.parent().remove();

	}

	if (textarea.size() == 0){
		jQuery('body').append('<textarea id="fullscreen"></textarea>');
		jQuery('#fullscreen').text(val);
		
		textarea = jQuery('#fullscreen');
		
		textarea.show();

		//var fsSetting = myMarkItUpSettings.markupSet[myMarkItUpSettings.markupSet.length - 1];
		var fsSetting = myMarkItUpSettings.markupSet[23];
		fsSetting.className = 'minimize';
		textarea.markItUp(myMarkItUpSettings);

		var container = textarea.parents('.markItUp');
		jQuery(container).jqm({toTop:true}).jqmShow();

		var textareaCSS = { width: '98%', height: '98%', margin: 'auto' }
		var containerCSS = { position: 'absolute', backgroundColor: '#ffffff', width: '98%', height: '98%', marginLeft: '1%', zIndex: 2999 }
		var headerCSS = { width: '98%', margin: 'auto'}
		var innerContainerCSS = { height: '90%' }
		var previewFrameCSS = { width: '98%', display: 'block', height: '98%', margin: 'auto'}

		textarea.css(textareaCSS);
		container.css(containerCSS);
		var closeBtn = '<a href="#" class="full_screen_close" style="position: absolute; right: 20px; top: 4px; font-weight: normal; font-family: Arial, sans-serif; font-size: 11px;">' + markitupLanguage('fullscreen_close') + '</a>';
		jQuery('.markItUpContainer', container).css(innerContainerCSS);

		jQuery('.markItUpHeader', container).css(headerCSS).append(closeBtn);

		jQuery.scrollTo('body', 800);

		var previewOn = false;
		var resizeHandlerBgImg = $('.markItUpResizeHandle', container).css('background');

		$('.preview', container).click(function(){
			previewOn = (previewOn) ? false : true;
			var previewFrame = jQuery('.markItUpPreviewFrame', container);
			previewFrame.css(previewFrameCSS);
			if (previewOn){
				// can't use hide because of FF errors
				textarea.css({ height: '0%'});
				previewFrame.css({ height: '98%', visibility: 'visible'});
			} else {
				textarea.css({ height: '98%'});
				previewFrame.css({ height: '0%', visibility: 'hidden'});
			}
		});

		// toggle maximize to minimize
		jQuery('.minimize', container).click(function(){
			minimize();
			return false;
		})

		jQuery('.full_screen_close', container).click(function(){
			minimize();
			return false;
		})
	} else {
	//	minimize();
	}

}

myMarkItUpSettings.markItUpImageInsert = function (markItUp){
	var url = jqx.config.fuelPath + '/assets/select/images/?selected=';
	var html = '<iframe src="' + url +'" id="asset_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; height: 480px; width: 850px;"></iframe>';
	$modal = fuel.modalWindow(html, 'inline_edit_modal', false);
	
	$modal.find('iframe#asset_inline_iframe').bind('load', function(){
		var iframeContext = this.contentDocument;
		$assetSelect = jQuery('#asset_select', iframeContext);
		$assetPreview = jQuery('#asset_preview', iframeContext);
		jQuery('.cancel', iframeContext).add('.modal_close').click(function(){
			$modal.jqmHide();
			var selectedVal = $assetSelect.val();
			var replace = '<img src="{img_path(\'' + selectedVal + '\')}" alt="" />';
			jQuery(markItUp.textarea).trigger('insertion', [{replaceWith: replace}]);
			return false;
		});
	});

}