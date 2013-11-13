//fuel = top.window.initFuelNamespace();
if (window.fuel == undefined){
	window.fuel = {};
}
fuel.fields = {};

// date picker field
fuel.fields.datetime_field = function(context){
	var o = {
		dateFormat : 'mm/dd/yy',
		firstDay : 0,
		minDate : null,
		maxDate : null,
		region : '',
		showButtonPanel : false,
		showOn: 'button',
	    buttonText: 'Click to show the calendar',
	    buttonImageOnly: true, 
	    buttonImage: jqx_config.imgPath + 'calendar.png'
	}
	$('.datepicker', context).each(function(i){
		var options = {
			dateFormat : $(this).attr('data-date_format'),
			region : $(this).attr('data-region'),
			minDate : $(this).attr('data-min_date'),
			maxDate : $(this).attr('data-max_date'),
			firstDay : $(this).attr('data-first_day')
		};
		var opts = $.extend(o, options);
		$.datepicker.regional[o.region];
		$(this).datepicker(opts);
	})
}

// multi combo box selector
fuel.fields.multi_field = function(context, inline_edit){

	var comboOptions = function(elem){
		var comboOpts = {};
		comboOpts.valuesEmptyString = fuel.lang('comboselect_values_empty');
		comboOpts.selectedEmptyString = fuel.lang('comboselect_selected_empty');
		comboOpts.defaultSearchBoxString = fuel.lang('comboselect_filter');
		
		var $sortingElem = $(elem).parent().find('.sorting');
		if ($sortingElem.length){
			comboOpts.autoSort = false;
			comboOpts.isSortable = true;
			comboOpts.selectedOrdering = eval(unescape($sortingElem.val()));
		}
		return comboOpts;
	}
	// set up supercomboselects
	$('select[multiple]', context).not('.no_combo').each(function(i){
		var comboOpts = comboOptions(this);
		$(this).supercomboselect(comboOpts);
	});
	
	if (inline_edit !== false){
		fuel.fields.inline_edit_field(context);
	}
}

// markItUp! and CKeditor field
fuel.fields.wysiwyg_field = function(context){
	$editors = $ckEditor = $('textarea', context).not('.no_editor, .markItUpEditor');
	var module = fuel.getModule();
	var _previewPath = myMarkItUpSettings.previewParserPath;

	var createMarkItUp = function(elem){
		var q = 'module=' + escape(module) + '&field=' + escape($(elem).attr('name'));
		if ($(elem).attr('data-preview')){
			q += '&preview=' + escape($(elem).attr('data-preview'));
		}
		myMarkItUpSettings.previewParserPath = _previewPath + '?' + q;
		
		if ($(elem).attr('data-markdown') == 1){
			var config = myMarkItUpMarkdownSettings;
		} else {
			var config = myMarkItUpSettings;
		}

		// add custom configs
		config = $.extend(config, $(elem).data());
		$(elem).not('.markItUpEditor').markItUp(config);

		
		// set the width of the preview to match the width of the textarea
		$('.markItUpPreviewFrame', context).each(function(){
			var width = $(this).parent().find('textarea').width();
			$(this).width(width);
		})
	}

	// fix ">" within template syntax
	var fixCKEditorOutput = function(elem){
		var elemVal = $(elem).val();
		var re = new RegExp('([=|-])&gt;', 'g');
		var newVal = elemVal.replace(re, '$1>');
		$(elem).val(newVal);
	}

	var CKEDitor_loaded = false;
	var createCKEditor = function(elem){
		if (typeof CKEDITOR == 'undefined') return;
		//window.CKEDITOR_BASEPATH = jqx_config.jsPath + 'editors/ckeditor/'; // only worked once in jqx_header.php file
		var ckId = $(elem).attr('id');
		var sourceButton = '<a href="#" id="' + ckId + '_viewsource" class="btn_field editor_viewsource">' + fuel.lang('btn_view_source') + '</a>';
		
		// used in cases where repeatable fields cause issues
		if ($(elem).hasClass('ckeditor_applied')) {
			return;
		}
		
		// cleanup
		if (CKEDITOR.instances[ckId]) {
			CKEDITOR.remove(CKEDITOR.instances[ckId]);
			//CKEDITOR.instances[ckId].destroy();
		}
		var config = jqx_config.ckeditorConfig;

		// add custom configs
		config = $.extend(config, $(elem).data());
		var hasCKEditorImagePlugin = (config.extraPlugins && config.extraPlugins.indexOf('fuelimage') != -1);

		
		CKEDITOR.replace(ckId, config);

		// add this so that we can set that the page has changed
		CKEDITOR.instances[ckId].on('instanceReady', function(e){

			editor = e.editor;
			this.document.on('keyup', function(e){
				editor.updateElement();
			});
			
			// set processors
			// http://docs.cksource.com/CKEditor_3.x/Howto/FCKeditor_HTML_Output
			var writer = this.dataProcessor.writer; 
			
			// The character sequence to use for every indentation step.
			writer.indentationChars = '    ';

			var dtd = CKEDITOR.dtd;
			// Elements taken as an example are: block-level elements (div or p), list items (li, dd), and table elements (td, tbody).
			for ( var e in CKEDITOR.tools.extend( {}, dtd.$block, dtd.$listItem, dtd.$tableContent ) )
			{
				editor.dataProcessor.writer.setRules( e, {
					// Indicates that an element creates indentation on line breaks that it contains.
					indent : false,
					// Inserts a line break before a tag.
					breakBeforeOpen : true,
					// Inserts a line break after a tag.
					breakAfterOpen : false,
					// Inserts a line break before the closing tag.
					breakBeforeClose : false,
					// Inserts a line break after the closing tag.
					breakAfterClose : true
				});
			}

			for ( var e in CKEDITOR.tools.extend( {}, dtd.$list, dtd.$listItem, dtd.$tableContent ) )
			{
				this.dataProcessor.writer.setRules( e, {			
					indent : true
				});
			}

			// You can also apply the rules to a single element.
			this.dataProcessor.writer.setRules( 'table',
			{ 		
				indent : true
			});	

			this.dataProcessor.writer.setRules( 'form',
			{ 		
				indent : true
			});

			// process image paths
			this.dataProcessor.htmlFilter.addRules( {
				elements : {
				    $ : function( element ) {
				    	
						// // Output dimensions of images as width and height attributes on src
						if ( element.name == 'img' && hasCKEditorImagePlugin) {
							var src = element.attributes['src'];
							img = src.replace(/^\{img_path\(([^\)]+)\)\}/, function(match, contents, offset, s) {
		   										return contents;
	    								}
									);
							img = img.replace(jqx_config.assetsImgPath, '');
							src = "{img_path(" + img + ")}";
							element.attributes.src = src;
							element.attributes['data-cke-saved-src'] = src;
				        }
				    }
				}
			});
			
			$elem = $('#' + ckId);
			
			// so we can check
			$elem.addClass('ckeditor_applied');
			// need so the warning doesn't pop up if you duplicate a value
			if ($.changeChecksaveValue){
				//$.changeChecksaveValue('#' + ckId, editor.getData());

				// just remove the checksave for these fields since it's too complicated until we figure out how to deal with all the processing on save
				$.removeChecksaveValue('#' + ckId);
			}

			// hack to force the width
			if ($elem.get(0).style.width){
				$elem.after('<div style="width:' + $elem.get(0).style.width+ '"></div>');
			}
		})
	
		// translate image paths
		$(elem).val(unTranslateImgPath($(elem).val()));

		CKEDITOR.instances[ckId].resetDirty();
		
		// needed so it doesn't update the content before submission which we need to clean up... 
		// our keyup event took care of the update
		CKEDITOR.config.autoUpdateElement = false;
		
		CKEDITOR.instances[ckId].hidden = false; // for toggling
	

		// add view source
		if ($('#' + ckId).parent().find('.editor_viewsource').length == 0){
			
			$('#' + ckId).parent().append(sourceButton);

			$('#' + ckId + '_viewsource').click(function(e){
				$elem = $(elem);
				ckInstance = CKEDITOR.instances[ckId];

				//if (!$('#cke_' + ckId).is(':hidden')){
				if (!ckInstance.hidden){
					ckInstance.hidden = true;
					if (!$elem.hasClass('markItUpEditor')){
						createMarkItUp(elem);
						$elem.show();
					}
					$('#cke_' + ckId).hide();
					$elem.css({visibility: 'visible'}).closest('.html').css({position: 'static'}); // used instead of show/hide because of issue with it not showing textarea
					//$elem.closest('.html').show();
				
					$('#' + ckId + '_viewsource').text(fuel.lang('btn_view_editor'));
				
					if (!ckInstance.checkDirty() && $.changeChecksaveValue){
						$.changeChecksaveValue('#' + ckId, ckInstance.getData())
					}

					// update the info
					ckInstance.updateElement();
				
				
				} else {

					ckInstance.hidden = false;
				
					$('#cke_' + ckId).show();
				
					$elem.closest('.html').css({position: 'absolute', 'left': '-100000px', overflow: 'hidden'}); // used instead of show/hide because of issue with it not showing textarea
					//$elem.show().closest('.html').hide();
					$('#' + ckId + '_viewsource').text(fuel.lang('btn_view_source'))
				
					var txt = unTranslateImgPath($elem.val());
					ckInstance.setData(txt);
				}
			
				fixCKEditorOutput(elem);
				return false;
			})
		}

	}
	
	var unTranslateImgPath = function(txt){
		txt = txt.replace(/\{img_path\(([^\)]+)\)\}/g, function(match, contents, offset, s) {
											contents = contents.replace(/'|"/g, '');
	   										return jqx_config.assetsImgPath + contents;
    								}
								);
		return txt;
	}	
	

	var unTranslateImgPath2 = function(editor){
		// translate img_path
		setTimeout(function(){
			var txt = editor.getData();
			txt = txt.replace(/\{img_path\('([^']+)'\)\}/g, function(match, contents, offset, s) {
		   										return jqx_config.assetsImgPath + contents;
	    								}
									);
			editor.setData(txt);
			editor.updateElement();

		}, 50)
	}	
	
	var createPreview = function(id){

		var $textarea = $('#' + id);
		var previewButton = '<a href="#" id="' + id + '_preview" class="btn_field editor_preview">' + fuel.lang('btn_preview') + '</a>';
	
		// add preview to make it noticable and consistent
		if ($textarea.parent().find('.editor_preview').length == 0){
		
			var $previewBtn = $textarea.parent('.markItUpContainer').find('.markItUpHeader .preview');
			if ($previewBtn){
				$textarea.parent().append(previewButton);

				$('#' + id + '_preview').click(function(e){
					e.preventDefault();
					var previewOptions = $textarea.data('preview_options');
					if (!previewOptions.length) previewOptions = 'width=1024,height=768';

					var previewWindow = window.open('', 'preview', previewOptions);
					var val = (typeof CKEDITOR != 'undefined' && CKEDITOR.instances[id] != undefined && $textarea.css('visibility') != 'visible') ? CKEDITOR.instances[id].getData() : $textarea.val();
					var csrf = $('#csrf_test_name').val();
					$.ajax( {
						type: 'POST',
						url: myMarkItUpSettings.previewParserPath,
						data: myMarkItUpSettings.previewParserVar+'='+encodeURIComponent(val) + '&csrf_test_name='+ csrf,
						success: function(data) {
							writeInPreview(data); 
						}
					});

					function writeInPreview(data) {
						if (previewWindow.document) {			
							try {
								sp = previewWindow.document.documentElement.scrollTop
							} catch(e) {
								sp = 0;
							}	
							previewWindow.document.open();
							previewWindow.document.write(data);
							previewWindow.document.close();
							previewWindow.document.documentElement.scrollTop = sp;
						}
					}
				});
			}
		}

	}

	$editors.each(function(i) {
		var _this = this;
		var ckId = $(this).attr('id');
		if ((jqx_config.editor.toLowerCase() == 'ckeditor' && !$(this).hasClass('markitup')) || $(this).hasClass('wysiwyg')){
			// createCKEditor(this);
			setTimeout(function(){
				createCKEditor(_this);
			}, 250) // hackalicious... to prevent CKeditor errors when the content is ajaxed in... this patch didn't seem to work http://dev.ckeditor.com/attachment/ticket/8226/8226_5.patch
		} else {
			createMarkItUp(this);
		}
		
		// setup update of element on save just in case
		$(this).parents('form').submit(function(){
			if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances[ckId] != undefined && CKEDITOR.instances[ckId].hidden == false){
				CKEDITOR.instances[ckId].updateElement();
			}
		})
		createPreview(ckId);
		
		
	});
}

// file upload field
fuel.fields.file_upload_field = function(context){
	
	// hackalicious... to prevent issues when things get ajaxed in
	setTimeout(function(){
		// setup multi-file naming convention
		$.fn.MultiFile.options.accept = jqx_config.assetsAccept;
		$multiFile = $('.multifile:file');

		// get accept types and then remove the attribute from the DOM to prevent issue with Chrome
		var acceptTypes = $multiFile.attr('accept');
		$multiFile.addClass('accept-' + acceptTypes); // accepts from class as well as attribute so we'll use the class instead
		$multiFile.removeAttr('accept');// for Chrome bug
		$multiFile.MultiFile({ namePattern: '$name___$i'});
	}, 500)
	
}

// asset select field
fuel.fields.asset_field = function(context, options){
	
	var selectedAssetFolder = 'images';
	var activeField = null;

	var showAssetsSelect = function(){
		var winHeight = 450;
		var url = jqx_config.fuelPath + '/assets/select/' + selectedAssetFolder + '/?selected=' + escape($('#' + activeField).val());
		var html = '<iframe src="' + url +'" id="asset_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; height: ' + winHeight + 'px; width: 850px;"></iframe>';
		$modal = fuel.modalWindow(html, 'inline_edit_modal', false);
		
		// // bind listener here because iframe gets removed on close so we can't grab the id value on close
		var $iframe = $modal.find('iframe#asset_inline_iframe');
		$iframe.bind('load', function(){
			var iframeContext = this.contentDocument;
			
			if (this.contentWindow.parent){
				var parentWindowHeight = $(this.contentWindow.parent.document).height();
				if (parentWindowHeight < winHeight){
					$iframe.height(parentWindowHeight - (parseInt($('#__FUEL_modal__').css('top')) + 20));
				}
			}

			$assetSelect = $('#asset_select', iframeContext);
			$assetPreview = $('#asset_preview', iframeContext);
			$('.cancel', iframeContext).add('.modal_close').click(function(){
				$modal.jqmHide();
				if ($(this).is('.save')){
					var $activeField = $('#' + activeField);
					var assetVal = jQuery.trim($activeField.val());
					var selectedVal = $assetSelect.val();
					var separator = $activeField.attr('data-separator');
					var multiple = parseInt($activeField.attr('data-multiple')) == 1;
					if (multiple){
						if (assetVal.length) assetVal += separator;
						assetVal += selectedVal;
					} else {
						assetVal = selectedVal;
					}
					$('#' + activeField).val(assetVal);
				}
				return false;
			});
		})
		return false;
	}
	
	
	var _this = this;
	$('.asset_select', context).each(function(i){
		if ($(this).parent().find('.asset_upload_button').length == 0){
			var assetFolder = $(this).data('folder');

			// legacy code
			if (!assetFolder) {
				var assetTypeClasses = ($(this).attr('class') != undefined) ? $(this).data('folder').split(' ') : [];
				var assetFolder = (assetTypeClasses.length > 1) ? assetTypeClasses[assetTypeClasses.length - 1] : 'images';
			}
			var btnLabel = '';
			switch(assetFolder.split('/')[0].toLowerCase()){
				case 'pdf':
					btnLabel = fuel.lang('btn_pdf');
					break;
				case 'images': case 'img': case '_img':
					btnLabel = fuel.lang('btn_image');
					break;
				case 'swf': case 'flash':
					btnLabel = fuel.lang('btn_flash');
					break;
				default :
					btnLabel = fuel.lang('btn_asset');
			}
			$(this).after('&nbsp;<a href="'+ jqx_config.fuelPath + '/assets/select/' + assetFolder + '" class="btn_field asset_select_button ' + assetFolder + '" data-folder="' + assetFolder + '">' + fuel.lang('btn_select') + ' ' + btnLabel + '</a>');
		}
	});

	$('.asset_select_button', context).click(function(e){
		activeField = $(e.target).parent().find('input,textarea:first').attr('id');
		selectedAssetFolder = $(e.target).data('folder');

		// legacy code
		if (!selectedAssetFolder){
			var assetTypeClasses = $(e.target).attr('class').split(' ');
			selectedAssetFolder = (assetTypeClasses.length > 0) ? assetTypeClasses[(assetTypeClasses.length - 1)] : 'images';
		}
		showAssetsSelect();
		return false;
	});
	
	
	// asset upload 
	var showAssetUpload = function(url){
		var html = '<iframe src="' + url +'" id="add_edit_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; height: 0px; width: 0px;"></iframe>';
		$modal = fuel.modalWindow(html, 'inline_edit_modal', true);
		
		// // bind listener here because iframe gets removed on close so we can't grab the id value on close
		$modal.find('iframe#add_edit_inline_iframe').bind('load', function(){
			var iframeContext = this.contentDocument;
			selected = $('#uploaded_file_name', iframeContext).val();
			if (selected && selected.length){
				$('#' + activeField).val(selected);
				$modal.jqmHide();
			}
		})
		return false;
	}
	$('.asset_upload', context).each(function(i){
		if ($(this).parent().find('.asset_upload_button').length == 0){
			var assetFolder = $(this).data('folder');

			// legacy code
			if (!assetFolder){
				var assetTypeClasses = ($(this).attr('class') != undefined) ? $(this).attr('class').split(' ') : [];
				var assetFolder = (assetTypeClasses.length > 1) ? assetTypeClasses[assetTypeClasses.length - 1] : 'images';
			}
			var btnLabel = fuel.lang('btn_upload_asset');
			$(this).after('&nbsp;<a href="'+ jqx_config.fuelPath + '/assets/inline_create/" class="btn_field asset_upload_button ' + assetFolder + '" data-params="' + $(this).attr('data-params') + '">' + btnLabel + '</a>');
		}
	});
	
	$('.asset_upload_button', context).click(function(e){
		activeField = $(e.target).parent().find('input:first').attr('id');
		selectedAssetFolder = $(e.target).data('folder');

		// legacy code
		if (!selectedAssetFolder){
			var assetTypeClasses = $(e.target).attr('class').split(' ');
			selectedAssetFolder = (assetTypeClasses.length > 0) ? assetTypeClasses[(assetTypeClasses.length - 1)] : 'images';
		}
		var params = $(this).attr('data-params');
		var url = $(this).attr('href') + '?' + params;
		showAssetUpload(url);
		return false;
		
	});
}

// inline editing of another module
fuel.fields.inline_edit_field = function(context){

	// fuel.fields.multi_field(context, false);

	var topWindowContext = window.top.document;
	
	var displayError = function($form, html){
		$form.find('.inline_errors').addClass('notification error ico_error').html(html).animate( { backgroundColor: '#ee6060'}, 1500);
	}
	
	var $modal = null;
	var selected = null;
	var editModule = function(url, onLoadCallback, onCloseCallback){
		var html = '<iframe src="' + url +'" id="add_edit_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; height: 0px; width: 0px;"></iframe>';
		$modal = fuel.modalWindow(html, 'inline_edit_modal', true, onLoadCallback, onCloseCallback);
		
		// bind listener here because iframe gets removed on close so we can't grab the id value on close
		$modal.find('iframe#add_edit_inline_iframe').bind('load', function(){
			var iframeContext = this.contentDocument;
			selected = $('#id', iframeContext).val();
		})
		return false;
	}
	
	$('.add_edit', context).each(function(i){
		var $field = $(this);
		var fieldId = $field.attr('id');
		var $form = $field.closest('form');
		var module = $field.data('module');
		
		var isMulti = ($field.attr('multiple')) ? true : false;
		
		var parentModule = fuel.getModuleURI(context);
		var url = jqx_config.fuelPath + '/' + module + '/inline_';
		
		if (!$field.parent().find('.add_inline_button').length) $field.after('&nbsp;<a href="' + url + 'create" class="btn_field add_inline_button">' + fuel.lang('btn_add') + '</a>');
		if (!$field.parent().find('.edit_inline_button').length) $field.after('&nbsp;<a href="' + url + 'edit/" class="btn_field edit_inline_button">' + fuel.lang('btn_edit') + '</a>');
		
		var refreshField = function($field){
			//$field = (field != undefined) ? field : $field;
			
			// redeclared here in case $field is set
			var fieldId = $field.attr('id');
			
			// if no value added,then no need to refresh
			if (!selected) return;
			var refreshUrl = jqx_config.fuelPath + '/' + parentModule + '/refresh_field';
			var params = { field:fieldId, field_id: fieldId, selected:selected};


			// fix for pages... a bit kludgy
			if (parentModule == 'pages'){
				params.layout = $('#layout').val();
			}
			
			// for template fields
			if ($field.data('orig_name')) {
				params.field = $field.data('orig_name');
			}
			params.index = $field.data('index');
			params.key = $field.data('key');
			params.field_name = $field.data('field_name');


			// for sortable fields
			var fieldName = $field.attr('name');
			fieldName = fieldName.replace('[', '\\[');
			fieldName = fieldName.replace(']', '\\]');

			var $form = $field.closest('form');

			$fieldContainer = $('#' + fieldId, context).closest('td.value');
			$field.closest('form').trigger('form-pre-serialize');
			var selector = '[name=' + fieldName + ']';

			// refresh value
			$field = $(selector);
			if ($field.length > 1){
				var val = [];
				$field.each(function(i){
					val.push($(this).val());
				})
			} else {
				var val = $field.val();
			}

			params.values = val;

			$.post(refreshUrl, params, function(html){
				$('#notification').html('<ul class="success ico_success"><li>Successfully added to module ' + module + '</li></ul>')
				fuel.notifications();
			
				$modal.jqmHide();
				if (html.length){
					$fieldContainer.empty();
					$fieldContainer.html(html)

					//$('#' + fieldId, context).replaceWith(html);
				}
				
				// already inited with custom fields
				
				//console.log($form.formBuilder())
				//$form.formBuilder().call('inline_edit');
				// refresh field with formBuilder jquery
				
				fuel.fields.multi_field(context)
				$('#form').formBuilder().initialize($('#' + fieldId, context));
				$('#' + fieldId, context).change(function(){
					changeField($(this));
				});
				changeField($('#' + fieldId, context));
			});
		}
		
		var changeField = function($this){
			if ($this.val() == '' || $this.find('option').length == 0){
				if ($this.is('select') && $this.find('option').length == 0){
					$this.hide();
				}
				if ($this.is('input, select')) $this.next('.btn_field').hide();
			} else {
				$this.next('.btn_field').show();
			}	
		}
		
		$('.add_inline_button', context).unbind().click(function(e){
			$field = $(this).parent().children(':first');
			editModule($(this).attr('href'), null, function(){ refreshField($('#' + $field.attr('id')))});
			$(context).scrollTo('body', 800);
			return false;
		});

		$('.edit_inline_button', context).unbind().click(function(e){
			var $elem = $(this).parent().find('select');
			var val = $elem.val();

			var fieldName = $elem.attr('name')
			fieldName = fieldName.replace('[', '');
			fieldName = fieldName.replace(']', '');
			var sortName = 'sorting_' + fieldName;
			var form = $(this).parents('form');

			if (!val){
				alert(fuel.lang('edit_multi_select_warning'));
				return false;
			}
			var editIds = val.toString().split(',');
			var $selected = $elem.parent().find('.supercomboselect_right li.selected:first');
			
			if ((!editIds.length || editIds.length > 1) && (!$selected.length || $selected.length > 1)) {
				alert(fuel.lang('edit_multi_select_warning'));
			} else {
				if ($selected.get(0) && $selected.length == 1){
					var id = $selected.attr('id');
					var idIndex = id.substr(id.lastIndexOf('_') + 1);
					var val = $elem.find('option').eq(idIndex).attr('value');
					var url = $(this).attr('href') + val;
				} else {
					var url = $(this).attr('href') + editIds[0];
				}
				$field = $(this).parent().children(':first');
				editModule(url, null, function(){ refreshField($field)});
			}
			return false;
		});

		$field.change(function(){
			changeField($(this));
		});
		changeField($field);
	});
}

// creates a field that will use apply it's value when typed it to another field after passing it through a transformation function
fuel.fields.linked_field = function(context){
	
	var _this = this;
	var module = fuel.getModule();

	var getFieldId = function(refId, context){
		var $key = $('input[data-key="' + refId + '"]', context);
		var id = ($key.length) ? $key.attr('id') : $('#' + refId, context).attr('id');
		return id;
	}
	
	// needed for enclosure
	var bindLinkedKeyup = function(slave, master, func){
		var slaveId = getFieldId(slave, context);
		var masterId = getFieldId(master, $('#' + slaveId).closest('.form'));

		if ($('#' + slaveId).val() == ''){
			$('#' + masterId).keyup(function(e){
				// for most common cases
				if (func){
					var newVal = func($(this).val());
					$('#' + slaveId).val(newVal);
				}

			});
		}
		
		// setup ajax on blur to do server side processing if no javascript function exists
		if (!func){
			$('#' + masterId).blur(function(e){
				var url = __FUEL_PATH__ + '/' + module + '/process_linked';
				var parameters = {
					master_field:master, 
					master_value:$(this).val(), 
					slave_field:slave
				};
				$.post(url, parameters, function(response){
					$('#' + slaveId).val(response);
				});
			});
		}
		
	}

	// needed for enclosure
	var bindLinked = function(slave, master, func){
		if ($('#' + getFieldId(slave, context)).val() == ''){
			if (typeof(master) == 'string'){
				var funcName = $('#' + getFieldId(slave, context)).data('formatter');
				if (!funcName) {
					var func = url_title;
				} else {
					if (this[funcName]){
						var func = this[funcName];
					} else if (window[funcName]){
						var func = window[funcName];
					}
				}
				bindLinkedKeyup(slave, master, func);
			} else if (typeof(master) == 'object'){
				for (var o in master){
					var func = false;
					var funcName = master[o];
					var val = $('#' + getFieldId(o, context)).val();
					if (funcName == 'url_title'){
						var func = url_title;
					// check for function scope, first check local function, then class, then global window object
					} else if (funcName != 'url_title'){
						if (this[funcName]){
							var func = this[funcName];
						} else if (window[funcName]){
							var func = window[funcName];
						}
					}
					bindLinkedKeyup(slave, o, func);
					break; // stop after first one
				}
			}
		}
	}
	
	$('.linked', context).each(function(i){

		// go all the way up to the value containing element because error messages insert HTML that won't allow us to use prev()
		var linkedInfo = $(this).closest('.value').find('.linked_info').text();
		if (linkedInfo.length){
			bindLinked($(this).attr('id'), eval('(' + linkedInfo + ')'));
		}
	});
	
}

// create number field
fuel.fields.number_field = function(context, options){
	$('.numeric', context).each(function(i){
		var o = {decimal: false, negative: false}
		o = $.extend(o, options);
		if ($(this).attr('data-decimal') == "1" || $(this).attr('data-decimal').toLowerCase() == "yes" || $(this).attr('data-decimal').toLowerCase() == "true"){
			o.decimal = '.';
		} else {
			o.decimal = false;
		}
		if ($(this).attr('data-negative') == "1" || $(this).attr('data-negative').toLowerCase() == "yes" || $(this).attr('data-negative').toLowerCase() == "true"){
			o.negative = true;
		} else {
			o.negative = false;
		}
		$(this).numeric(o);
	});
}

// create currency field
fuel.fields.currency_field = function(context, options){
	$('.currency', context).each(function(i){
		var o = {aSep: ',', aDec: '.',  dGroup: 3, vMin: 0.00, vMax: 999999999.99}
		o = $.extend(o, options);
		if ($(this).attr('data-separator')){
			o.aSep = $(this).attr('data-separator');
		}
		if ($(this).attr('data-decimal')){
			o.aDec = $(this).attr('data-decimal');
		}
		if ($(this).attr('data-grouping')){
			o.dGroup = $(this).attr('data-grouping');
		}
		if ($(this).attr('data-min')){
			o.vMin = $(this).attr('data-min');
		}
		if ($(this).attr('data-max')){
			o.vMax = $(this).attr('data-max');
		}
		$(this).autoNumeric(o);
	});
}

// create a repeatable field
fuel.fields.template_field = function(context, options){
	
	if (!options) options = {};

	var repeatable = function($repeatable){
		
		// set individual options based on the data-max attribute
		$repeatable.each(function(i){
			var $attrElem = ($(this).is('.repeatable_container')) ? $(this) : $(this).closest('.repeatable_container');
			options.max = $attrElem.attr('data-max');
			options.min = $attrElem.attr('data-min');
			options.dblClickBehavior = $attrElem.attr('data-dblclick');
			options.initDisplay = $attrElem.attr('data-init_display');
			options.addButtonText = fuel.lang('btn_add_another');
			options.removeButtonText = fuel.lang('btn_remove');
			options.warnBeforeDeleteMessage = fuel.lang('warn_before_delete_msg');
			$(this).repeatable(options);
		})
	}
	// get nested ones first
	$nestedElems = $('.repeatable .repeatable').parent();
	repeatable($nestedElems);

	// then the parents
	$parentElems = $('.repeatable').not('.repeatable .repeatable').parent();
	repeatable($parentElems);

	$(document).off('sortStopped').on('sortStarted', function(){
		fuel.fields.sortStarted();
	})
	$(document).off('sortStopped').on('sortStopped', function(){
		fuel.fields.sortStopped();
	})

	// used event namespace http://api.jquery.com/event.namespace/
	// Remove clone event	
	$(document).off('cloned.fuel', '.repeatable_container');

	// Add another event handler	
	$(document).on('cloned.fuel', '.repeatable_container', fuel.fields.clonedFunc)

}

// hack to prevent CKEditor issues
fuel.fields.sortStarted = function(){

	var currentCKTexts = {};
	if (typeof CKEDITOR != 'undefined'){
		for(var n in CKEDITOR.instances){
			currentCKTexts[n] = CKEDITOR.instances[n].getData();
			$('#' + n).removeClass('ckeditor_applied');
			CKEDITOR.instances[n].destroy();
		}
	}
}

fuel.fields.sortStopped = function(){
	if (typeof CKEDITOR != 'undefined'){
		// can't pass context because we do a global destroy on CKEditor fields
		fuel.fields.wysiwyg_field();
	}
}

fuel.fields.clonedFunc = function(e){
	$('#form').formBuilder().initialize(e.clonedNode);

	// Hacktastic to remove any loader icons left on from fuel.fields.block
	e.clonedNode.find('.loader').hide();

	// to help with CKEditor issues... UGH!!!
	setTimeout(function(){
		fuel.fields.sortStarted();
		fuel.fields.sortStopped();
	}, 300)
}

// url select field
fuel.fields.url_field = function(context, options){
	
	var activeField = null;

	var showUrlSelect = function(){
		$activeField = $('#' + activeField);
		var url = jqx_config.fuelPath + '/pages/select/?selected=' + escape($activeField.val());
		
		if ($activeField.data('input')){
			url += '&input=' + $activeField.val();
		}
		if ($activeField.data('target')){
			url += '&target=' + $activeField.data('target');
		}
		if ($activeField.data('title')){
			url += '&title=' + $activeField.data('title');	
		}
		if ($activeField.data('pdfs')){
			url += '&pdfs=1';	
		}
		if ($activeField.data('filter')){
			url += '&filter=' + $activeField.data('filter');	;	
		}
		var html = '<iframe src="' + url +'" id="url_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; width: 850px;"></iframe>';
		$modal = fuel.modalWindow(html, 'inline_edit_modal', true);
		
		// // bind listener here because iframe gets removed on close so we can't grab the id value on close
		var $iframe = $modal.find('iframe#url_inline_iframe');
		$iframe.bind('load', function(){
			var iframeContext = this.contentDocument;
			
			$('.cancel', iframeContext).add('.modal_close').click(function(){
				$modal.jqmHide();
				if ($(this).is('.save')){
					var $activeField = $('#' + activeField);
					$input =  $('#input', iframeContext);
					$urlSelect = $('#url_select', iframeContext);
					$target = $('#target', iframeContext);
					$title = $('#title', iframeContext);
					$selected = $('#selected', iframeContext);

					var selectedUrl = ($input.length && $input.val().length) ? $input.val() : $urlSelect.val();
					if ($target.length || $title.length) {
						var isHTTP = (selectedUrl.match(/^\w+:\/\//)) ? true : false;
						var selectedVal = '<a href="';
						if (!isHTTP) selectedVal += '{site_url(\'';
						selectedVal += selectedUrl;
						if (!isHTTP) replace += '\')}';
						selectedVal += '"';

						if ($target.length && $target.val() != '_self'){
							selectedVal += ' target="' + $target.val() + '"';
						}
						if ($title.length && $title.val().length){
							selectedVal += ' title="' + $title.val() + '"';
						}
						selectedVal += '>' + $selected.val() + '</a>';
					} else {
						var selectedVal = $urlSelect.val();	
					}
					
					$('#' + activeField).val(selectedVal);
				}
				return false;
			});
		})
		return false;
	}
	
	
	var _this = this;
	$('.url_select', context).not('.no_url').each(function(i){
		if ($(this).parent().find('.url_select_button').length == 0){
			$(this).after('&nbsp;<a href="'+ jqx_config.fuelPath + '/pages/select" class="btn_field url_select_button">' + fuel.lang('btn_select') + '</a>');
		}
	});

	$('.url_select_button', context).click(function(e){
		activeField = $(e.target).parent().find('input,textarea:first').attr('id');
		showUrlSelect();
		return false;
	});
	
}

fuel.fields.block = function(context, options){
	$(context).on('change', '.block_layout_select', function(e){
		var val = $(this).val();
		var url = $(this).data('url');
		if (!url) url = '';

		// for pages inline editing
		var module = $('#__fuel_module__');
		var context = $(this).attr("name");
		if (module.length && module.val() == 'pagevariables'){
			var id = $('#page_id').val();
			var name = $(this).attr("name").replace(/^value/, $('#name').attr("value"));
		} else {
			var id = $('#__fuel_id__').val();
			var name = '';
		}

		if (url.length){
			url = eval(unescape(url));
		} else {
			var layout = $(this).val();
			if (layout && layout.length){
				//layout = layout.split('/').pop();
				layout = layout.replace('/', ':');
				url = jqx_config.fuelPath + '/blocks/layout_fields/' + layout + '/' + id+ '/english/';
			}
		}
		
		// var contextArr = context.split("--")
		// if (contextArr.length > 1){
		// 	context = contextArr.pop();
		// }
		$layout_fields = $(this).next('.block_layout_fields');
		if (url.length){
			url += '?context=' + context + '&name=' + name;

			// show loader
			$(this).parent().find('.loader').show();
			$layout_fields.load(url, function(){
				// hide loader
				$(this).parent().find('.loader').hide();
				$(this).find('.block_name').val(val);
				fuel.adjustIframeWindowSize();	
				
			});
		} else {
			$layout_fields.empty();
		}


	})
	
	$('.block_layout_select', context).change();

}
