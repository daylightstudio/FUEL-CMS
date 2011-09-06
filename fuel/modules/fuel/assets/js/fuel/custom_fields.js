//fuel = top.window.initFuelNamespace();
if (fuel == undefined){
	var fuel = {};
}
fuel.fields = {};

// date picker field
fuel.fields.datetime_field = function(context, options)
{
	var o = {
		format : 'mm/dd/yyyy',
		firstDayOfWeek : 0,
		startDate : '01/01/2000',
		endDate : '12/31/2100',
	}
	o = $.extend(o, options);
	Date.format = o.format;
	Date.firstDayOfWeek = o.firstDayOfWeek;

	$('.datepicker', context).fillin(o.format);
	$('.datepicker_hh', context).fillin('hh');
	$('.datepicker_mm', context).fillin('mm');
	//$('.datepicker').datePicker();
	var dpOptions = {startDate: o.endDate, endDate: o.startDate}

	$('.datepicker', context).filter(":not('.dp-applied')").each(function(i){
		if ($(this).val() != Date.format){
			var d = $(this).val();
			var picker = $(this).datePicker(dpOptions).dpSetSelected(d);
		} else {
			var picker = $(this).datePicker(dpOptions);
		}
		picker.bind(
			'dateSelected', 
			function(e, selectedDates) {
				$(this).removeClass("fillin");
			}
		);

	});
}

// multi combo box selector
fuel.fields.multi_field = function(context){
	
	var comboOptions = function(elem){
		var comboOpts = {};
		comboOpts.valuesEmptyString = fuel.lang('comboselect_values_empty');
		comboOpts.selectedEmptyString = fuel.lang('comboselect_selected_empty');
		comboOpts.defaultSearchBoxString = fuel.lang('comboselect_filter');
		var sortingId = 'sorting_' + $(elem).attr('id');
		if ($('#' + sortingId).size()){
			comboOpts.autoSort = false;
			comboOpts.isSortable = true;
			comboOpts.selectedOrdering = eval(unescape($('#' + sortingId).val()));
		}
		return comboOpts;
	}
	// set up supercomboselects
	$('select[multiple]', context).not('select[class=no_combo]').each(function(i){
		var comboOpts = comboOptions(this);
		$(this).supercomboselect(comboOpts);
	});
}

// markItUp! and CKeditor field
fuel.fields.wysiwyg_field = function(context){
	
	var selector = 'textarea:not(textarea[class=no_editor])';
	$editors = $ckEditor = $(selector, context);
	var module = fuel.getModule();
	var _previewPath = myMarkItUpSettings.previewParserPath;

	var createMarkItUp = function(elem){
		var q = 'module=' + escape(module) + '&field=' + escape($(elem).attr('name'));
		var markitUpClass = $(elem).attr('className');
		if (markitUpClass.length){
			var previewPath = markitUpClass.split(' ');
			if (previewPath.length && previewPath[0] != 'no_editor'){
				q += '&preview=' + previewPath[previewPath.length - 1];
			}
		}
		myMarkItUpSettings.previewParserPath = _previewPath + '?' + q;
		$(elem).markItUp(myMarkItUpSettings);
	}
	
	// fix ">" within template syntax
	var fixCKEditorOutput = function(elem){
		var elemVal = $(elem).val();
		var re = new RegExp('([=|-])&gt;', 'g');
		var newVal = elemVal.replace(re, '$1>');
		$(elem).val(newVal);
	}
	
	var createCKEditor = function(elem){
		//window.CKEDITOR_BASEPATH = jqx.config.jsPath + 'editors/ckeditor/'; // only worked once in jqx_header.php file
		var ckId = $(elem).attr('id');
		var sourceButton = '<a href="#" id="' + ckId + '_viewsource" class="btn_field editor_viewsource">' + fuel.lang('btn_view_source') + '</a>';
		// cleanup
		if (CKEDITOR.instances[ckId]) {
			CKEDITOR.remove(CKEDITOR.instances[ckId]);
		}
		CKEDITOR.replace(ckId, jqx.config.ckeditorConfig);
		// add this so that we can set that the page has changed
		CKEDITOR.instances[ckId].on('instanceReady', function(e){
			editor = e.editor;
			this.document.on('keyup', function(e){
				editor.updateElement();
			});
			
			// so the formatting doesn't get too crazy from ckeditor
			this.dataProcessor.writer.setRules( 'p',
			{
				indent : false,
				breakBeforeOpen : true,
				breakAfterOpen : false,
				breakBeforeClose : false,
				breakAfterClose : true
			});
		})
		CKEDITOR.instances[ckId].resetDirty();
		
		// needed so it doesn't update the content before submission which we need to clean up... 
		// our keyup event took care of the update
		CKEDITOR.config.autoUpdateElement = false;
		
		CKEDITOR.instances[ckId].hidden = false; // for toggline
		
		$('#' + ckId).parent().append(sourceButton);

		$('#' + ckId + '_viewsource').click(function(){
			$elem = $(elem);
			ckInstance = CKEDITOR.instances[ckId];

			//if (!$('#cke_' + ckId).is(':hidden')){
			if (!CKEDITOR.instances[ckId].hidden){
				CKEDITOR.instances[ckId].hidden = true;
				if (!$elem.hasClass('markItUpEditor')){
					createMarkItUp(elem);
					$elem.show();
				}
				$('#cke_' + ckId).hide();
				$elem.css({visibility: 'visible'}).closest('.html').css({position: 'static'}); // used instead of show/hide because of issue with it not showing textarea
				//$elem.closest('.html').show();
				
				$('#' + ckId + '_viewsource').text(fuel.lang('btn_view_editor'));
				
				if (!ckInstance.checkDirty()){
					$.changeChecksaveValue(ckId, ckInstance.getData())
				}

				// update the info
				ckInstance.updateElement();
				
				
			} else {
				CKEDITOR.instances[ckId].hidden = false;
				
				$('#cke_' + ckId).show();
				
				$elem.closest('.html').css({position: 'absolute', 'left': '-100000px', overflow: 'hidden'}); // used instead of show/hide because of issue with it not showing textarea
				//$elem.show().closest('.html').hide();
				$('#' + ckId + '_viewsource').text(fuel.lang('btn_view_source'))
				
				ckInstance.setData($elem.val());
			}
			
			fixCKEditorOutput(elem);
			return false;
		})

		
	}
	
	$editors.each(function(i) {
		var ckId = $(this).attr('id');
		if ((jqx.config.editor.toLowerCase() == 'ckeditor' && $(this).is('textarea[class!="markitup"]')) || $(this).hasClass('wysiwyg')){
			createCKEditor(this);
		} else {
			createMarkItUp(this);
		}
		
		// setup update of element on save just in case
		$(this).parents('form').submit(function(){
			if (CKEDITOR && CKEDITOR.instances[ckId] != undefined && CKEDITOR.instances[ckId].hidden == false){
				CKEDITOR.instances[ckId].updateElement();
			}
		})
	});
}

// file upload field
fuel.fields.file_upload_field = function(context){
	// setup multi-file naming convention
	$.fn.MultiFile.options.accept = jqx.config.assetsAccept;
	$multiFile = $('.multifile:file');
	
	// get accept types and then remove the attribute from the DOM to prevent issue with Chrome
	var acceptTypes = $multiFile.attr('accept');
	$multiFile.addClass('accept-' + acceptTypes); // accepts from class as well as attribute so we'll use the class instead
	$multiFile.removeAttr('accept');// for Chrome bug
	$multiFile.MultiFile({ namePattern: '$name___$i'});
	
}

// asset select field
fuel.fields.asset_field = function(context){
	
	var selectedAssetFolder = 'images';
	var activeField = null;
	
	var showAssetsSelect = function(){
		$('#asset_modal').jqm({
			ajax: jqx.config.fuelPath + '/assets/select_ajax/' + selectedAssetFolder,
		 	onLoad: function(){
				$('#asset_select').val($('#' + activeField).val());
				if (!$('#asset_select').val()) $('#asset_select').val($('#asset_select').children(':first').attr('value'));
				var isImg = ($('#asset_select').val() && $('#asset_select').val().match(/\.jpg$|\.jpeg$|\.gif$|\.png$/));
				//if (_this.assetFolder == 'images'){
				if (isImg){
					$('#asset_select').change(function(e){
						$('#asset_preview').html('<img src="' + jqx.config.assetsPath + selectedAssetFolder + '/' + $('#asset_select').val() + '" />');
					})
					$('#asset_select').change();
				} else {
					$('#asset_preview').hide();
				}
				
				$('.ico_yes').click(function(){
					$('#asset_modal').jqmHide();
					$('#' + _this.activeField).val($('#asset_select').val());
					return false;
				});
				$('.ico_no').click(function(){
					$('#asset_modal').jqmHide();
					return false;
				});
			}
		}).jqmShow();
		return false;
		
	}
	
	var _this = this;
	$('.asset_select', context).each(function(i){
		var assetTypeClasses = $(this).attr('className').split(' ');
		var assetFolder = (assetTypeClasses.length > 1) ? assetTypeClasses[assetTypeClasses.length - 1] : 'images';
		var btnLabel = '';
		switch(assetFolder.split('/')[0].toLowerCase()){
			case 'pdf':
				btnLabel = fuel.lang('btn_pdf');
				break;
			case 'images': case 'img': case '_img':
				btnLabel = fuel.lang('btn_image');
				break;
			case 'swf': case 'flash':
				btnLabel = fuel.lang('btn_flast');
				break;
			default :
				btnLabel = fuel.lang('btn_asset');
		}
		$(this).after('&nbsp;<a href="'+ jqx.config.fuelPath + '/assets/select_ajax/' + assetFolder + '" class="btn_field asset_select_button ' + assetFolder + '">' + fuel.lang('btn_select') + ' ' + btnLabel + '</a>');
	});
	if (!$('#asset_modal').size()){
		$('body').append('<div id="asset_modal" class="jqmWindow"></div>');
	}
	$('.asset_select_button', context).click(function(e){
		activeField = $(e.target).prev().attr('id');
		var assetTypeClasses = $(e.target).attr('className').split(' ');
		selectedAssetFolder = (assetTypeClasses.length > 0) ? assetTypeClasses[(assetTypeClasses.length - 1)] : 'images';
		return showAssetsSelect();
	});
}

// for editing related modules value from within the context of a module
fuel.fields.inline_edit_field_old = function(context){
	
	var displayError = function($form, html){
		$form.find('.inline_errors').addClass('notification error ico_error').html(html).animate( { backgroundColor: '#ee6060'}, 1500);
	}
	
	var editModule = function(url, callback){
		var _this = this;
		//if ($('#add_edit_inline_modal').size() == 0){
			$('body').append('<div id="add_edit_inline_modal" class="jqmWindow"><div class="loader"></div></div>');
		//}
		var $modalContext = $('#add_edit_inline_modal');
		$modalContext.jqm({
			ajax: url,
		 	onLoad: function(){
				var $form = $modalContext.find('form');
				$form.attr('action', url);
				$form.submit(function(e){
					if (e.which !== 13)
					{
						$('.ico_save', $modalContext).click();
					}
					return false;
				});
				
				$('.modal_cancel', $modalContext).click(function(){
					$modalContext.jqmHide();
					return false;
				})
				$('.ico_save', $modalContext).click(function(){
					$.removeChecksave();
					
					$form.ajaxSubmit({
						success: function(html){
							if ($(html).is('error')){
								displayError($form, html);
							} else if (callback){
								callback(html);
							}
						}
					});
					return false;
				});
				$('.delete', $modalContext).click(function(){
					$.removeChecksave();
					
					if (confirm(fuel.lang('confirm_delete'))){
						$form.find('.__fuel_inline_action__').val('delete');
						$form.ajaxSubmit({
							success: function(html){
								if ($(html).is('error')){
									displayError($form, html);
								} else if (callback){
									callback(html);
								}
							}
						});
					}
					return false;
				});
				
				
				//_this.initSpecialFields($modalContext);
			}
		}).jqmShow();
	}
	
	$('.add_edit', context).each(function(i){
		var $field = $(this);
		var fieldId = $field.attr('id');
		var $form = $field.closest('form:first');
		var className = $field.attr('className').split(' ');
		var module = '';
		if (className.length > 1){
			module = className[className.length -1];
		} else {
			module = fieldId.substr(0, fieldId.length - 3) + 's'; // eg id = client_id so module would be clients
		}
		var url = jqx.config.fuelPath + '/' + module + '/inline_';
		$field.after('&nbsp;<a href="' + url + 'create" class="btn_field add_inline_button">' + fuel.lang('btn_add') + '</a>');
		$field.after('&nbsp;<a href="' + url + 'edit/' + $field.val() + '" class="btn_field edit_inline_button">' + fuel.lang('btn_edit') + '</a>');
		
		
		var refreshField = function(html){
			var refreshUrl = jqx.config.fuelPath + '/' + _this.module + '/refresh_field';
			var params = { field:fieldId, field_id: fieldId, values: $field.val(), selected:html};
			$.post(refreshUrl, params, function(html){
				$('#notification').html('<ul class="success ico_success"><li>Successfully added to module ' + module + '</li></ul>')
				//_this._notifications();
				$modalContext.jqmHide();
				$('#' + fieldId).replaceWith(html);
				
				// already inited with custom fields
				$form.formBuilder()
				// refresh field with formBuilder jquery
				
				
				/*if ($('#' + fieldId + '[multiple]').not('select[class=no_combo]').size()){
					var comboOpts = _this._comboOps(this);
					$('#' + fieldId).supercomboselect(comboOpts);
				}*/
				$('#' + fieldId).change(function(){
					changeField($(this));
				});
				changeField($('#' + fieldId));
			});
		}
		
		var changeField = function($this){
			if (($this.val() == '' || $this.attr('multiple')) || $this.find('option').size() == 0){
				if ($this.is('select') && $this.find('option').size() == 0){
					$this.hide();
				}
				if ($this.is('input, select')) $this.next('.btn_field').hide();
			} else {
				$this.next('.btn_field').show();
			}	
		}
		
		
		if ($('#add_edit_inline_modal').size() == 0){
			$('body').append('<div id="add_edit_inline_modal" class="jqmWindow"><div class="loader"></div></div>');
		}
		var $modalContext = $('#add_edit_inline_modal');
		
		$('.add_inline_button', context).click(function(e){
			editModule($(this).attr('href'), refreshField);
			return false;
		});

		$('.edit_inline_button', context).click(function(e){
			editModule(url + $(this).prev().val(), refreshField);
			return false;
		});

		$field.change(function(){
			changeField($(this));
		});
		changeField($field);
	});
}

fuel.fields.inline_edit_field = function(context){


	var comboOptions = function(elem){
		var comboOpts = {};
		comboOpts.valuesEmptyString = fuel.lang('comboselect_values_empty');
		comboOpts.selectedEmptyString = fuel.lang('comboselect_selected_empty');
		comboOpts.defaultSearchBoxString = fuel.lang('comboselect_filter');
		var sortingId = 'sorting_' + $(elem).attr('id');
		if ($('#' + sortingId).size()){
			comboOpts.autoSort = false;
			comboOpts.isSortable = true;
			comboOpts.selectedOrdering = eval(unescape($('#' + sortingId).val()));
		}
		return comboOpts;
	}
	// set up supercomboselects
	$('select[multiple]', context).not('select[class=no_combo]').each(function(i){
		var comboOpts = comboOptions(this);
		$(this).supercomboselect(comboOpts);
	});



	var topWindowContext = window.top.document;
	
	var displayError = function($form, html){
		$form.find('.inline_errors').addClass('notification error ico_error').html(html).animate( { backgroundColor: '#ee6060'}, 1500);
	}
	
	var $modal = null;
	var selected = null;
	var editModule = function(url, onLoadCallback, onCloseCallback){
		var html = '<iframe src="' + url +'" id="add_edit_inline_iframe" frameborder="0" scrolling="no" style="border: none; height: 0px; width: 0px;"></iframe>';
		$modal = fuel.modalWindow(html, 'inline_edit_modal', onLoadCallback, onCloseCallback);
		
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
		var className = $field.attr('className').split(' ');
		var module = '';
		
		var isMulti = ($field.attr('multiple')) ? true : false;
		
		if (className.length > 1){
			module = className[className.length -1];
		} else {
			module = fieldId.substr(0, fieldId.length - 3) + 's'; // eg id = client_id so module would be clients
		}
		var parentModule = fuel.getModuleURI(context);
		
		var url = jqx.config.fuelPath + '/' + module + '/inline_';
		var addCss = 'add_inline_button';
		$field.after('&nbsp;<a href="' + url + 'create" class="btn_field ' + addCss + '">' + fuel.lang('btn_add') + '</a>');
		$field.after('&nbsp;<a href="' + url + $field.val() + '" class="btn_field edit_inline_button">' + fuel.lang('btn_edit') + '</a>');
		if (isMulti) addCss += ' float_left';
		
		var refreshField = function(){
			
			// if no value added,then no need to refresh
			if (!selected) return;

			var refreshUrl = jqx.config.fuelPath + '/' + parentModule + '/refresh_field';
			var params = { field:fieldId, field_id: fieldId, values: $field.val(), selected:selected};
			$.post(refreshUrl, params, function(html){
				$('#notification').html('<ul class="success ico_success"><li>Successfully added to module ' + module + '</li></ul>')
				//_this._notifications();
				$modal.jqmHide();
				$('#' + fieldId, context).replaceWith(html);
				
				// already inited with custom fields
				
				//console.log($form.formBuilder())
				//$form.formBuilder().call('inline_edit');
				// refresh field with formBuilder jquery
				
				fuel.fields.multi_field(context)
				
				$('#' + fieldId, context).change(function(){
					changeField($(this));
				});
				changeField($('#' + fieldId, context));
			});
		}
		
		var changeField = function($this){
			if (($this.val() == '' || $this.attr('multiple')) || $this.find('option').size() == 0){
				if ($this.is('select') && $this.find('option').size() == 0){
					$this.hide();
				}
				if ($this.is('input, select')) $this.next('.btn_field').hide();
			} else {
				$this.next('.btn_field').show();
			}	
		}
		
		$('.add_inline_button', context).click(function(e){
			editModule($(this).attr('href'), null, refreshField);
			return false;
		});

		$('.edit_inline_button', context).click(function(e){
			//editModule(url + $(this).prev().val(), refreshField);
			//editModule($form, url + $(this).prev().val(), refreshField);
			
			return false;
		});

		$field.change(function(){
			changeField($(this));
		});
		changeField($field);
	});
}

fuel.fields.linked_field = function(context){
	
	var _this = this;
	var module = fuel.getModule();
	
	// needed for enclosure
	var bindLinkedKeyup = function(slave, master, func){
		var slaveId = fuel.getFieldId(slave, context);
		var masterId = fuel.getFieldId(master, context);
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

		if ($('#' + fuel.getFieldId(slave, context)).val() == ''){
			if (typeof(master) == 'string'){
				bindLinkedKeyup(slave, master, url_title);
			} else if (typeof(master) == 'object'){

				for (var o in master){
					var func = false;
					var funcName = master[o];
					var val = $('#' + fuel.getFieldId(o, context)).val();
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
		var linkedInfo = $(this).parents('.value').find('.linked_info').text();
		if (linkedInfo.length){
			bindLinked($(this).attr('id'), eval('(' + linkedInfo + ')'));
		}
	});
	//{"slug":{"name":"url_title"}
	// if (this.initObj.linked_fields){
	// 	var linked = this.initObj.linked_fields;
	// 	for(var n in linked){
	// 		bindLinked(n, linked[n]);
	// 	}
	// }
}

// create fillin property fields... placeholder value is really all you need though and this may be deprecated
fuel.fields.fillin_field = function(context){
	$('.fillin').each(function(i){
		var placeholder = unescape($(this).attr('placeholder'));
		$(this).fillin(placeholder);
	});
}