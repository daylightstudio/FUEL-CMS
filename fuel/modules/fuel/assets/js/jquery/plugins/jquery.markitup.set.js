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
myMarkItUpSettings = {
	root: 'skins/simple/',
	nameSpace:           "html", // Useful to prevent multi-instances CSS conflict
    previewParserPath:   __FUEL_PATH__ + "/preview",
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [ 	
		{name:'Bold', key:'B', className:'bold', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
		{name:'Italic', key:'I', className:'italic', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
		{name:'Stroke through', className:'stroke', key:'S', openWith:'<del>', closeWith:'</del>' },
		{separator:'---------------' },
    	{name:'Paragraph', className:'p', key:'P', openWith:'<p>', closeWith:'</p>'}, 
        {name:'Heading 1', className:'h1', key:'1', openWith:'<h1>', closeWith:'</h1>', placeHolder:'Your title here...' },
        {name:'Heading 2', className:'h2', key:'2', openWith:'<h2>', closeWith:'</h2>', placeHolder:'Your title here...' },
        {name:'Heading 3', className:'h3',key:'3', openWith:'<h3>', closeWith:'</h3>', placeHolder:'Your title here...' },
        {name:'Heading 4', className:'h4', key:'4', openWith:'<h4>', closeWith:'</h4>', placeHolder:'Your title here...' },
        {separator:'---------------' },
		{name:'Numeric List', className:'ol', key:'', openWith:'<ol>', closeWith:'</ol>' },
		{name:'Bullet List', className:'ul', key:'', openWith:'<ul>', closeWith:'</ul>'  },
		{name:'List Item', className:'li', key:'', openWith:'<li>', closeWith:'</li>' },
		{name:'Block Quote', className:'blockquote', key:'', openWith:'<blockquote>', closeWith:'</blockquote>' },
		{name:'Horizontal Rule', className:'hr', key:'', openWith:'<hr />'},
		{separator:'---------------' },
		/*{name:'Image', className:'image', key:'I', replaceWith:'<img src="{img_path}\[![Source:!:]!]\" alt="[![Alternative text]!]" />' },*/
		{name: 'Image', className: 'image', key: 'L', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpImageInsert(marketItup); 
				return false;
			}
		},
		{name:'Link', className:'link', key:'L', openWith:'<a href="{site_url(\'[![Link:!:]!]\')}" title="[![Title]!]" target="[![Target:!:_self]!]">', closeWith:'</a>', placeHolder:'Your text to link...'},
		
		{name:'Mailto', className:'mailto', key:'M', openWith:'{safe_mailto("', closeWith:'")}', placeHolder:'email@address.com' },
		{name:'PHP Variable', className:'fuel_var', key:'', openWith:'{$[![PHP variable:!:]!]', closeWith:'}', placeHolder:'' },
		{separator:'---------------' },
		{name:'Clean', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },		
		{name:'Preview toggle(hold down alt/option and click to preview)', className:'preview',  call:'preview'},
		{name: 'FullScreen', className: 'maximize', key: 'F', replaceWith: 
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
		var closeBtn = '<a href="#" class="full_screen_close" style="position: absolute; right: 20px; top: 4px; font-weight: normal; font-family: Arial, sans-serif; font-size: 11px;">Close</a>';
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
	var isInline = (jQuery('#__FUEL__asset_modal').size());
	var path = (isInline) ? __FUEL_PATH__ + '/assets/select_ajax/images' : jqx.config.fuelPath + '/assets/select_ajax/images';
	var imgPath = (isInline) ? __FUEL_INIT_PARAMS__.assetsImgPath : jqx.config.assetsImgPath;
	jQuery('#asset_modal, #__FUEL__asset_modal').jqm({
		ajax: path,
	 	onLoad: function(){

			jQuery('#asset_select').change(function(e){
				jQuery('#asset_preview').html('<img src="' + imgPath + jQuery('#asset_select').val() + '" />');
			})
			jQuery('#asset_select').change();
			
			jQuery('.ico_yes').click(function(){
				jQuery('#asset_modal,#__FUEL__asset_modal').jqmHide();
				var replace = '<img src="{img_path(\'' + jQuery('#asset_select').val() + '\')}" alt="" />';
				jQuery(markItUp.textarea).trigger('insertion', [{replaceWith: replace}]);
				return false;
			})

			jQuery('.ico_no').click(function(){
				jQuery('#asset_modal,#__FUEL__asset_modal').jqmHide();
				return false;
			})
			
			
		}
	}).jqmShow();
}