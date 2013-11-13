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
	if (typeof(window['__FUEL_LOCALIZED__']) != 'undefined')
	{
		return __FUEL_LOCALIZED__['markitup_' + key];
	}
	else if (typeof(window['jqx']) != 'undefined')
	{
		return jqx.config.localized['markitup_' + key];
	}
}


myMarkItUpSettings = {
	root: 'skins/simple/',
	nameSpace:           "html", // Useful to prevent multi-instances CSS conflict
    previewParserPath:  "fuel/preview", // will be __FUEL_PATH__ + "/preview" after custom field renders
	previewInWindow: true,
	previewParserVar: 'data',
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
		{name:markitupLanguage('img'), className: 'image', key: 'I', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpImageInsert(marketItup); 
				return false;
			}
		},
		/*{name:markitupLanguage('link'), className:'link', key:'L', openWith:'<a href="{site_url(\'[![' + markitupLanguage('link') + ':!:]!]\')}" target="[![' + markitupLanguage('target') + ':!:_self]!]">', closeWith:'</a>', placeHolder:markitupLanguage('placeholder_link')},*/
		{name:markitupLanguage('link'), className: 'link', key: 'L', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpLinkInsert(marketItup); 
				return false;
			}
		},
		{name:markitupLanguage('mailto'), className:'mailto', key:'M', openWith:'{safe_mailto(', closeWith:')}', placeHolder:markitupLanguage('placeholder_email') },
		{name:markitupLanguage('php'), className:'fuel_var', key:'', openWith:'{$[![' + markitupLanguage('php') + ':!:]!]', closeWith:'}', placeHolder:'' },
		{name:markitupLanguage('clean'), className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },		
		{separator:'---------------' },
		//{name:markitupLanguage('preview'), className:'preview',  call:'preview'},
		{name: markitupLanguage('fullscreen'), className: 'maximize', key: 'F', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpFullScreen(marketItup); 
				return false;
			}
		},
	]
}

myMarkItUpMarkdownSettings = {
	root: 'skins/simple/',
	nameSpace:           "html", // Useful to prevent multi-instances CSS conflict
    previewParserPath:  "fuel/preview", // will be __FUEL_PATH__ + "/preview" after custom field renders
	previewInWindow: true,
	previewParserVar: 'data',
	onShiftEnter:       {keepDefault:false, openWith:'\n\n'},
	markupSet:  [ 	
		{name:markitupLanguage('b'), key:'B', className:'bold', openWith:'**', closeWith:'**' },
		{name:markitupLanguage('i'), key:'I', className:'italic', openWith:'_', closeWith:'_' },
		{separator:'---------------' },
        {name:markitupLanguage('h1'), className:'h1', key:'1', closeWith:function(markItUp) { return myMarkItUpSettings.markdownTitle(markItUp, '=') }, placeHolder:markitupLanguage('placeholder_headings') },
        {name:markitupLanguage('h2'), className:'h2', key:'2', closeWith:function(markItUp) { return myMarkItUpSettings.markdownTitle(markItUp, '-') }, placeHolder:markitupLanguage('placeholder_headings') },
        {name:markitupLanguage('h3'), className:'h3',key:'3', openWith:'### ', placeHolder:markitupLanguage('placeholder_headings') },
        {name:markitupLanguage('h4'), className:'h4', key:'4', openWith:'#### ', placeHolder:markitupLanguage('placeholder_headings') },
        {separator:'---------------' },
		{name:markitupLanguage('ol'), className:'ol', key:'', openWith:function(markItUp) {
            return markItUp.line+'. ';
        } },
		{name:markitupLanguage('ul'), className:'ul', key:'', openWith:'- ' },
		{name:markitupLanguage('blockquote'), className:'blockquote', key:'', openWith:'> ' },
		{name:markitupLanguage('hr'), className:'hr', key:'', openWith:'***'},
		{separator:'---------------' },
		/*{name:'Image', className:'image', key:'I', replaceWith:'<img src="{img_path}\[![Source:!:]!]\" alt="[![Alternative text]!]" />' },*/
		{name:markitupLanguage('img'), className: 'image', key: 'I', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpImageInsert(marketItup); 
				return false;
			}
		},
		/*{name:markitupLanguage('link'), className:'link', key:'L', openWith:'<a href="{site_url(\'[![' + markitupLanguage('link') + ':!:]!]\')}" target="[![' + markitupLanguage('target') + ':!:_self]!]">', closeWith:'</a>', placeHolder:markitupLanguage('placeholder_link')},*/
		{name:markitupLanguage('link'), className: 'link', key: 'L', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpLinkInsert(marketItup); 
				return false;
			}
		},

		{name:markitupLanguage('mailto'), className:'mailto', key:'M', openWith:'{safe_mailto(', closeWith:')}', placeHolder:markitupLanguage('placeholder_email') },
		{name:markitupLanguage('php'), className:'fuel_var', key:'', openWith:'{$[![' + markitupLanguage('php') + ':!:]!]', closeWith:'}', placeHolder:'' },
		{name:markitupLanguage('clean'), className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },		
		{separator:'---------------' },
		//{name:markitupLanguage('preview'), className:'preview',  call:'preview'},
		{name: markitupLanguage('fullscreen'), className: 'maximize', key: 'F', replaceWith: 
			function(marketItup){ 
				myMarkItUpSettings.markItUpFullScreen(marketItup); 
				return false;
			}
		},
	]
}

	//
myMarkItUpSettings.markdownTitle = function(markItUp, char) {
    heading = '';
    n = $.trim(markItUp.selection||markItUp.placeHolder).length;
    for(i = 0; i < n; i++) {
        heading += char;
    }
    return '\n'+heading+'\n';
}
myMarkItUpSettings.markItUpFullScreen = function (markItUp, display){
	
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
		var fsSetting = myMarkItUpSettings.markupSet[22];
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

		var previewOn = $.data(textarea, false);
		var resizeHandlerBgImg = $('.markItUpResizeHandle', container).css('background');
		
		if (!myMarkItUpSettings.previewInWindow){
			$('.preview', container).click(function(){
				previewOn = ($.data(textarea)) ? false : true;
				var previewFrame = jQuery('.markItUpPreviewFrame', container);
				previewFrame.css(previewFrameCSS);
				if (previewOn){
					// can't use hide because of FF errors
					textarea.css({ height: '0%', minHeight: '0%'});
					previewFrame.css({ height: '98%', visibility: 'visible'});
				} else {
					textarea.css({ height: '98%'});
					previewFrame.css({ height: '0%', visibility: 'hidden'});
				}
				$.data(textarea, previewOn)
			});
		}

		// toggle maximize to minimize
		jQuery('.minimize', container).click(function(){
			minimize();
			return false;
		})

		jQuery('.full_screen_close', container).click(function(){
			minimize();
			return false;
		})

		jQuery('.jqmOverlay').click(function(){
			minimize();
			return false;
		})
	} else {
	//	minimize();
	}

}

myMarkItUpSettings.parseAttrs = function(str, attr){
	var regEx = new RegExp(' ' + attr + '="([^"]+?)"');
	var arr = str.match(regEx);
	var ret = '';
	if (arr && arr.length >= 1) {
		ret = arr[1];
	}
	return ret;
}

myMarkItUpSettings.markItUpImageInsert = function (markItUp){

	var selected = markItUp.selection;

	var isImgSelected = selected.match(/\<img[^>]+src=[^>]+>/);

	// if anchor selected then we start grabbing values
	var width, height, alt, align, className, imgFolder, imgOrder;

	if (isImgSelected){

		// set target
		var srcArr = selected.match(/ href="([^"]+?)"/);
		if (srcArr && srcArr.length >= 1) {
			src = srcArr[1];
			selected = src.replace(/^\{img_path\('(.+)'\)\}/g, function(match, contents, offset, s) {
		   										return contents;
	    								}
									);
		}

		// set attrs
		width = myMarkItUpSettings.parseAttrs(selected, 'width');
		height = myMarkItUpSettings.parseAttrs(selected, 'height');
		alt = myMarkItUpSettings.parseAttrs(selected, 'alt');
		align = myMarkItUpSettings.parseAttrs(selected, 'align');
		className = myMarkItUpSettings.parseAttrs(selected, 'class');

		// set title
		if (isImgSelected && isImgSelected.length >= 1) {
			selected = isImgSelected[1];
		}
	}
	imgFolder = jQuery(markItUp.textarea).attr('data-img_folder');
	imgOrder = jQuery(markItUp.textarea).attr('data-img_order');

	myMarkItUpSettings.displayAssetInsert(escape(selected), {width: width, height: height, alt: alt, align: align, className: className, imgFolder: imgFolder, imgOrder: imgOrder}, function(replace){
		jQuery(markItUp.textarea).trigger('insertion', [{replaceWith: replace}]);
	});
}


myMarkItUpSettings.displayAssetInsert = function (selected, attrs, callback){
	var folder = 'images';
	var imgFolder = attrs['imgFolder'];
	if (imgFolder){
		if (imgFolder.substr(0, 1) != '/'){
			folder += '/';
		}
		folder += imgFolder;
	}
	var url = jqx.config.fuelPath + '/assets/select/' + folder + '?nocache=' + new Date().getTime();
	if (selected) url += '&selected=' + escape(selected);
	url += '&width=' + ((attrs.width) ? attrs.width : '');
	url += '&height=' + ((attrs.height) ? attrs.height : '');
	url += '&alt=' + ((attrs.alt) ? escape(attrs.alt) : '');
	url += '&align=' + ((attrs.align) ? attrs.align : '');
	url += '&class=' + ((attrs.className) ? attrs.className : '');
	url += '&order=' + ((attrs.imgOrder) ? attrs.imgOrder : '');
	var loaded = false;
	var html = '<iframe src="' + url +'" id="asset_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; height: 500px; width: 850px;"></iframe>';
	$modal = fuel.modalWindow(html, 'inline_edit_modal', false);
	
	$modal.find('iframe#asset_inline_iframe').bind('load', function(){
		if (loaded) return;
		// to prevent double loading issue
		//$(this).unbind();
		var loaded = false;
		var iframeContext = this.contentDocument;
		$assetSelect = jQuery('#asset_select', iframeContext);
		$assetPreview = jQuery('#asset_preview', iframeContext);

		$width =  $('#width', iframeContext);
		$height = $('#height', iframeContext);
		$alt = $('#alt', iframeContext);
		$align = $('#align', iframeContext);
		$class = $('#class', iframeContext);

		jQuery('.cancel', iframeContext).add('.modal_close').click(function(){
			$modal.jqmHide();

			if ($(this).is('.save')){
				var selectedVal = $assetSelect.val();
				var isHTTP = false; // for later
				var replace = '<img src="';
				if (!isHTTP) replace += '{img_path(\'';
				if (imgFolder && imgFolder.length){
					replace += imgFolder + '/';	
				}
				replace += selectedVal;

				if (!isHTTP) replace += '\')}';
				replace += '"';
				if ($width.length && $width.val().length){
					replace += ' width="' + $width.val() + '"';
				}
				
				if ($height.length && $height.val().length){
					replace += ' height="' + $height.val() + '"';
				}
				
				if ($align.length && $align.val().length){
					replace += ' align="' + $align.val() + '"';
				}

				if ($class.length && $class.val().length){
					replace += ' class="' + $class.val() + '"';
				}

				replace += ' alt="' + $alt.val() + '"';
	
				replace += ' />';

				callback(replace);
			}
			return false;
		});
		loaded = true;
	});

}


myMarkItUpSettings.markItUpLinkInsert = function (markItUp){
	var selected = markItUp.selection;

	var isAnchorSelected = selected.match(/\<a[^>]+href=[^>]+>(.*)<\/a>/);

	// if anchor selected then we start grabbing values
	var input, target, title, className, linkPdfs;

	if (isAnchorSelected){

		// set input
		var hrefArr = selected.match(/ href="([^"]+?)"/);
		if (hrefArr && hrefArr.length >= 1) {
			href = hrefArr[1];
			input = href.replace(/^\{site_url\('(.*)'\)\}/g, function(match, contents, offset, s) {
		   										return contents;
	    								}
									);
		}

		// set attrs
		target = myMarkItUpSettings.parseAttrs(selected, 'target');
		title = myMarkItUpSettings.parseAttrs(selected, 'title');
		className = myMarkItUpSettings.parseAttrs(selected, 'class');

		// set title
		if (isAnchorSelected && isAnchorSelected.length >= 1) {
			selected = isAnchorSelected[1];
		}
	}
	linkPdfs = jQuery(markItUp.textarea).attr('data-link_pdfs');
	myMarkItUpSettings.displayLinkEditWindow(escape(selected), {input: input, target: target, title: title, className: className, linkPdfs: linkPdfs}, function(replace){
		jQuery(markItUp.textarea).trigger('insertion', [{replaceWith: replace}]);
	})
}

myMarkItUpSettings.displayLinkEditWindow = function(selected, attrs, callback){
	var url = jqx.config.fuelPath + '/pages/select/?nocache=' + new Date().getTime();
	if (selected) url += '&selected=' + escape(selected);
	url += '&input=' + ((attrs.input) ? attrs.input : '');
	url += '&target=' + ((attrs.target) ? attrs.target : '');
	url += '&title=' + ((attrs.title) ? attrs.title : '');
	url += '&class=' + ((attrs.className) ? attrs.className : '');
	url += '&pdfs=' + ((attrs.linkPdfs) ? attrs.linkPdfs : '');

	var html = '<iframe src="' + url +'" id="url_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; width: 850px;"></iframe>';
	$modal = fuel.modalWindow(html, 'inline_edit_modal', true);

	$modal.find('iframe#url_inline_iframe').bind('load', function(){
		
		// to prevent double loading issue
		$(this).unbind();

		var iframeContext = this.contentDocument;
			
		jQuery('.cancel', iframeContext).add('.modal_close').click(function(){
			$modal.jqmHide();
			if ($(this).is('.save')){
				$urlSelect = $('#url_select', iframeContext);
				$input =  $('#input', iframeContext);
				$target = $('#target', iframeContext);
				$title = $('#title', iframeContext);
				$class = $('#class', iframeContext);

				$selected = $('#selected', iframeContext);
				var selectedUrl = ($input.length && $input.val().length) ? $input.val() : $urlSelect.val();
				var isHTTP = (selectedUrl.match(/^\w+:\/\//)) ? true : false;
				var replace = '<a href="';
				
				if (selectedUrl.substr(0, 1) != '{') {
					if (selectedUrl.match(/\.pdf$/)){
						replace += '{pdf_path(\'' + selectedUrl + '\')}';
					} else {
						if (!isHTTP) replace += '{site_url(\'';
						replace += selectedUrl;
						if (!isHTTP) replace += '\')}';
					}
				} else {
					replace += selectedUrl;
				}

				replace += '"';
				if ($target.length && ($target.val().length != '' && $target.val() != '_self')){
					replace += ' target="' + $target.val() + '"';
				}
				if ($title.length && $title.val().length){
					replace += ' title="' + $title.val() + '"';
				}
				if ($class.length && $class.val().length){
					replace += ' class="' + $class.val() + '"';
				}
				replace += '>' + $selected.val() + '</a>';

				callback(replace);
			}
			return false;
		});
	});
}