/*
These are no longer needed because they've been extracted out into the page header to 
allow for optimization
jqx.load('plugin', 'date');
jqx.load('plugin', 'jquery.datePicker');
jqx.load('plugin', 'jquery.fillin');
jqx.load('plugin', 'jquery.markitup.pack');
jqx.load('plugin', 'jquery.markitup.set');
jqx.load('plugin', 'jquery.easing');
//jqx.load('plugin', 'jquery.dimensions');
jqx.load('plugin', 'jquery.bgiframe');
jqx.load('plugin', 'jquery.tooltip');
jqx.load('plugin', 'jquery.scrollTo-min');
jqx.load('plugin', 'jqModal');
jqx.load('plugin', 'jquery.checksave');
jqx.load('plugin', 'jquery.form');
jqx.load('plugin', 'jquery.treeview.min');
jqx.load('plugin', 'jquery.cookie');
jqx.load('plugin', 'jquery.hotkeys');

jqx.load('plugin', 'jquery-ui-1.8.4.custom.min');
jqx.load('plugin', 'jquery.disable.text.select.pack');
jqx.load('plugin', 'jquery.selso');
jqx.load('plugin', 'jquery.fillin');
jqx.load('plugin', 'jquery.supercomboselect');
*/

fuel.controller.BaseFuelController = jqx.lib.BaseController.extend({
	
	init : function(initObj){
		this._init(initObj);
		this._super(initObj);
	},
	
	_init : function(initObj){
		this.module = initObj.module;
		this.activeField = null;
		this.assetFolder = 'images';
		this.pageVals = {};
		this.cache = new jqx.Cache();
		this.modulePath = jqx.config.fuelPath + '/' + this.module;
		this.tableAjaxURL = this.modulePath + '/items/';
		this.treeAjaxURL = this.modulePath + '/items_tree/';
		this.precedenceAjaxURL = this.modulePath + '/items_precedence/';
		this.tableLoaded = false;
		this.rearrangeOn = false;
		this.leftMenuInited = false;
		this.formController = null;
		this.previewPath = myMarkItUpSettings.previewParserPath;
		this.localized = jqx.config.localized;
		
		this._submit();
		this._initLeftMenu();
		this._initTopMenu();
		this._initModals();
	},
	
	_initLeftMenu : function(){
		if (this.leftMenuInited) return;
		var leftNavTogglers = function(id, index){
			$('#' + id + ' h3').bind('click', {id:id,index:index},
				function(e){
					var nav = $(this).parent().find('ul');
					if (!nav.isHidden()){
						nav.hide();
						$(this).addClass('closed');
						var cookieVal = 1;
					} else {
						nav.show();
						$(this).removeClass('closed');
						var cookieVal = 0;
					}
					var leftNavCookie = $.cookie('fuel_leftnav');
					if (leftNavCookie){
						var cookieVals = leftNavCookie.split('|');
						cookieVals[e.data['index']] = cookieVal;
						leftNavCookie = cookieVals.join('|');
						$.cookie('fuel_leftnav', leftNavCookie, {path:jqx.config.cookieDefaultPath});
					}
					return false;
				}
			);
		}
		
		var ids = [];
		$('#fuel_left_panel_inner h3').each(function(i){
			var id = $(this).parent().attr('id');
			ids.push(id);
		});
		
		// create a cookie to remember state
		if (ids.length){
			var leftNavCookie;
			if (!$.cookie('fuel_leftnav')){
				$.cookie('fuel_leftnav', '0|0|0|0', {path:jqx.config.cookieDefaultPath});
			}
			var leftNavCookie = $.cookie('fuel_leftnav');
			var cookieVals = leftNavCookie.split('|');
			for (var i = 0; i < ids.length; i++){
				leftNavTogglers(ids[i], i);
				if (parseInt(cookieVals[i])){
					$('#' + ids[i] + ' h3').click();
				}
			}
			this.leftMenuInited = true;
		}
	},
	
	_initTopMenu : function(){
		$('#topnav li').hover(function(e){
			$('ul', this).show();
		}, 	function(e){
				$('ul', this).hide();
		});
	},
	
	_initModals : function(){
		$('.jqmWindow').jqm({modal:true,toTop:true});
		$('.jqmWindowShow').jqmShow();
	},

	_notifications : function(){

		// flash any notifications
		$(".notification .success").stop(true, true).animate( { backgroundColor: '#dcffb8'}, 1500);
		$(".notification .error").stop(true, true).animate( { backgroundColor: '#ee6060'}, 1500);
		$(".notification .warning").stop(true, true).animate( { backgroundColor: '#ffff99'}, 1500);

	},
	
	_submit : function(){
		$('#submit').click(function(){
			$('#form').submit();
			return false;
		});
	},
	
	items : function()
	{
		var _this = this;
		this.treeLoaded = false;
		this.tableLoaded = false;
		this.rearrangeOn = false;
		
		this._notifications();
		$('#search_term').fillin(this.lang('label_search')).focus();
		$('#limit').change(function(e){
			$('#form_table').submit();
		});
		
		if ($('#tree').exists()){
			var itemViewsCookieId = 'fuel_' + _this.module + '_items';
			var itemViewsCookie = $.cookie(itemViewsCookieId);
			
			$('#toggle_tree').click(function(e){
				
				$('#toggle_tree').parent().addClass('active');
				$('#toggle_list').parent().removeClass('active');
				$('#list_container').hide();
				$('#tree_container').show();
				$('#pagination').hide();
				$('#view_type').val('tree');
				$.cookie(itemViewsCookieId, $('#view_type').val(), {path:jqx.config.cookieDefaultPath});
				// lazy load tree
				if (!_this.treeLoaded){
					_this.redrawTree();
				}
				return false;
			});
			$('#toggle_list').click(function(e){

				$('#toggle_list').parent().addClass('active');
				$('#toggle_tree').parent().removeClass('active');
				$('#list_container').show();
				$('#tree_container').hide();
				$('#pagination').show();
				$('#view_type').val('list');
				$.cookie(itemViewsCookieId, $('#view_type').val(), {path:jqx.config.cookieDefaultPath});
				// lazy load table
				if (!_this.tableLoaded){
					_this.redrawTable();
				}
				return false;
			});

			if ($.cookie(itemViewsCookieId) == 'tree'){
				$('#toggle_tree').click();
				
			} else {
				$('#toggle_list').click();
			}
			
			// bind keyboard shortcuts
			$(document).bind('keydown', jqx.config.keyboardShortcuts.toggle_view, function(e){ 
				if ($('#list_container').isHidden()){
					$('#toggle_list').click(); 
				} else {
					$('#toggle_tree').click();
				}
				return false;
			});
			
		} else {
			this.redrawTable();
		}
		
		$('#form_table').submit(function(){
			$('#toggle_list').click();
		});
		
		$('#multi_delete').click(function(){
			$('#toggle_list').unbind('click');
			var deleteUrl = _this.modulePath + '/delete/';
			$('#form_table').attr('action', deleteUrl).submit();
			return false;
		});
		
		$('.multi_delete').live('click', function(e){
			if ($('.multi_delete:checked').size()){
				$('#multi_delete').css({display: 'block'});
			} else {
				$('#multi_delete').hide();
			}
		});
		$('#multi_delete').hide();
		
		$('#rearrange').live('click', function(e){
			$(this).parent().toggleClass('active');
			if ($(this).parent().hasClass('active')){
				_this.rearrangeOn = true;
				_this.rearrangeItems();
				$('#notification').append('<div class="ico_warn warning rearrange">' + _this.lang('rearrange_on') + '</div>');
				
			} else {
				_this.rearrangeOn = false;
				$("#data_table").removeClass('rearrange');
				$('#notification .rearrange').remove();
				
			}
			
			return false;
		});
		
		
	},
	
	add_edit : function(initSpecFields){
		if (initSpecFields == null) initSpecFields = true;
		var _this = this;
		$('.tooltip').tooltip({
			delay: 0,
			showURL: false,
			id: '__fuel_tooltip__'
		});
		this._notifications();
		//this._submit();
		
		if (initSpecFields) this.initSpecialFields($('#main_content_inner'));
		
		$('.publish_action').click(function(e){
			$.removeChecksave();
			if ($('#published').size() > 0){
				$('#published').val('yes');
			} else {
				$('#published_yes').attr('checked', true);
			}
			$('#form').submit();
			return false;
		});

		$('.unpublish_action').click(function(e){
			$.removeChecksave();
			if ($('#published').size() > 0){
				$('#published').val('no');
			} else {
				$('#published_no').attr('checked', true);
			}
			$('#form').submit();
			return false;
		});

		$('.activate_action').click(function(e){
			$.removeChecksave();
			if ($('#active').size() > 0){
				$('#active').val('yes');
			} else {
				$('#active_yes').attr('checked', true);
			}
			
			$('#form').submit();
			return false;
		});

		$('.deactivate_action').click(function(e){
			$.removeChecksave();
			if ($('#active').size() > 0){
				$('#active').val('no');
			} else {
				$('#active_no').attr('checked', true);
			}
			$('#form').submit();
			return false;
		});
		
		$('.duplicate_action').click(function(e){
			$('#id').val('dup');
			$('#form').attr('action', _this.modulePath + '/create');
			$('#form').submit();
			return false;
		});
		
		$('.delete_action').click(function(e){
			$.removeChecksave();
		});
		
		$('.save, #' + this.lang('btn_save')).live('click', function(e){
			$.removeChecksave();
			$('#form').submit();
			return false;
		});
		
		$('.cancel, #' + this.lang('btn_cancel')).live('click', function(e){
			_this.go(_this.modulePath);
			return false;
		});
		
		$('.submit_action').click(function(){
			$.removeChecksave();
			$('#form').attr('action', $(this).attr('href')).submit();
			return false;
		});
		
		
		$('#version').change(function(e){
			$.removeChecksave();
			if ($(this).val() != ''){
				if (confirm('Restoring previous data will overwrite the currently saved data. Are you sure you want to continue?')){
					$('#restore_form').submit();
				}
			}
		});
		
		$('#others').change(function(e){
			$.removeChecksave();
			if ($(this).val() != ''){
				window.location = _this.modulePath + '/edit/' + $(this).val();
			}
		});

		// keyboard shortcuts
		$(document).bind('keydown', jqx.config.keyboardShortcuts.save, function(e){ 
			$('.save').click();
			return false;
		});
		
		$(document).bind('keydown', jqx.config.keyboardShortcuts.view, function(e){ 
			window.location = ($('.view_action').attr('href'));
		});
		
		//$('#form input:first').select();
		$('#form input:first').focus();
		
		if (jqx.config.warnIfModified) $.checksave();
	},
	
	_comboOps : function(elem){
		var comboOpts = {};
		comboOpts.valuesEmptyString = this.lang('comboselect_values_empty');
		comboOpts.selectedEmptyString = this.lang('comboselect_selected_empty');
		comboOpts.defaultSearchBoxString = this.lang('comboselect_filter');
		var sortingId = $(elem).next().attr('id');
		if (sortingId && $('#' + sortingId).size()){
			comboOpts.autoSort = false;
			comboOpts.isSortable = true;
			comboOpts.selectedOrdering = eval(unescape($('#' + sortingId).val()));
		}
		
		return comboOpts;
	},
	
	initSpecialFields : function(context){
		var _this = this;
		this._initAssets(context);
		this._initAddEditInline(context);
		this._initDatePicker(context);
		this._initEditors(context);
		this._initViewPage();
		this._initLinkedFields(context);
		
		$('#form input:first', context).select();
		
		// set up supercomboselects
		$('select[multiple]', context).not('select[class=no_combo]').each(function(i){
			var comboOpts = _this._comboOps(this);
			$(this).supercomboselect(comboOpts);
		});
		
		// setup multi-file naming convention
		$.fn.MultiFile.options.accept = jqx.config.assetsAccept;
		$multiFile = $('.multifile:file');
		
		// get accept types and then remove the attribute from the DOM to prevent issue with Chrome
		var acceptTypes = $multiFile.attr('accept');
		$multiFile.addClass('accept-' + acceptTypes); // accepts from class as well as attribute so we'll use the class instead
		$multiFile.removeAttr('accept');// for Chrome bug
		$multiFile.MultiFile({ namePattern: '$name___$i'});
	},
	
	_getjQueryPluginOptions : function(elem){
		var opts = {};
		var cssClasses = $(elem).attr('className').split(' ');
		for(var i = 0; i < cssClasses.length; i++){
			if (cssClasses[i].substr(0, 7) == 'jqopts='){
				var jqOptions = cssClasses[i].substr(7);
				try{
					eval('opts=' + jqOptions);
				}catch(e){
					// fail silently... don't let this be your mantra!
				}
			}
		}
		return opts;
	},
	
	showAssetsSelect : function(){
		var _this = this;
		$('#asset_modal').jqm({
			ajax: jqx.config.fuelPath + '/assets/select_ajax/' + _this.assetFolder,
		 	onLoad: function(){
				$('#asset_select').val($('#' + _this.activeField).val());
				if (!$('#asset_select').val()) $('#asset_select').val($('#asset_select').children(':first').attr('value'));
				var isImg = $('#asset_select').val().match(/\.jpg$|\.jpeg$|\.gif$|\.png$/);
				//if (_this.assetFolder == 'images'){
				if (isImg){
					$('#asset_select').change(function(e){
						$('#asset_preview').html('<img src="' + jqx.config.assetsPath + _this.assetFolder + '/' + $('#asset_select').val() + '" />');
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
		
	},
	
	_initAssets : function(context){
		var _this = this;
		$('.asset_select', context).each(function(i){
			var assetTypeClasses = $(this).attr('className').split(' ');
			var assetFolder = (assetTypeClasses.length > 1) ? assetTypeClasses[1] : 'images';
			var btnLabel = '';
			if (assetFolder.split('/')[0] != undefined){
				switch(assetFolder.split('/')[0].toLowerCase()){
					case 'pdf':
						btnLabel = _this.lang('btn_pdf');
						break;
					case 'images': case 'img': case '_img':
						btnLabel = _this.lang('btn_image');
						break;
					case 'swf': case 'flash':
						btnLabel = _this.lang('btn_flash');
						break;
					default :
						btnLabel = _this.lang('btn_asset');
				}
			}
			$(this).after('&nbsp;<a href="'+ jqx.config.fuelPath + '/assets/select_ajax/' + assetFolder + '" class="btn_field asset_select_button ' + assetFolder + '">' + _this.lang('btn_select') + ' ' + btnLabel + '</a>');
		});
		if (!$('#asset_modal').size()){
			$('body').append('<div id="asset_modal" class="jqmWindow"></div>');
		}
		$('.asset_select_button', context).click(function(e){
			_this.activeField = $(e.target).prev().attr('id');
			var assetTypeClasses = $(e.target).attr('className').split(' ');
			_this.assetFolder = (assetTypeClasses.length > 0) ? assetTypeClasses[(assetTypeClasses.length - 1)] : 'images';
			return _this.showAssetsSelect();
		});
	},
	
	_initDatePicker : function(context){
		// set up any date fields
		Date.format = 'mm/dd/yyyy';
		Date.firstDayOfWeek = 0;
		
		$('.datepicker', context).fillin('mm/dd/yyyy');
		$('.datepicker_hh', context).fillin('hh');
		$('.datepicker_mm', context).fillin('mm');
		//$('.datepicker').datePicker();
		var dpOptions = {startDate: '01/01/2000', endDate: '12/31/2100'}
		$('.datepicker', context).filter(":not('.dp-applied'),:not(input[disabled='disabled'])").each(function(i){
			if (!$(this).attr('disabled') && !$(this).attr('readonly')){
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
			}
		});
	},
	
	_initAddEditInline : function(context){
		var _this = this;

		$('.add_edit', context).each(function(i){
			var $field = $(this);
			var fieldId = $field.attr('id');
			var className = $field.attr('className').split(' ');
			var module = '';
			if (className.length > 1){
				module = className[1];
			} else {
				module = fieldId.substr(0, fieldId.length - 3) + 's'; // eg id = client_id so module would be clients
			}
			var url = jqx.config.fuelPath + '/' + module + '/inline_edit/';
			$field.after('&nbsp;<a href="' + url + 'create" class="btn_field add_inline_button">' + _this.lang('btn_add') + '</a>');
			$field.after('&nbsp;<a href="' + url + $field.val() + '" class="btn_field edit_inline_button">' + _this.lang('btn_edit') + '</a>');
			
			
			var refreshField = function(html){
				var refreshUrl = jqx.config.fuelPath + '/' + _this.module + '/refresh_field';
				var params = { field:fieldId, field_id: fieldId, values: $field.val(), selected:html};
				$.post(refreshUrl, params, function(html){
					$('#notification').html('<ul class="success ico_success"><li>Successfully added to module ' + module + '</li></ul>')
					_this._notifications();
					$modalContext.jqmHide();
					$('#' + fieldId).replaceWith(html);
					if ($('#' + fieldId + '[multiple]').not('select[class=no_combo]').size()){
						var comboOpts = _this._comboOps(this);
						$('#' + fieldId).supercomboselect(comboOpts);
					}
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
				_this.editModule($(this).attr('href'), refreshField);
				return false;
			});

			$('.edit_inline_button', context).click(function(e){
				_this.editModule(url + $(this).prev().val(), refreshField);
				return false;
			});

			$field.change(function(){
				changeField($(this));
			});
			changeField($field);
		});
	},
	
	_initEditors : function(context){
		var _this = this;
		var selector = 'textarea:not(textarea[class=no_editor])';
		$editors = $ckEditor = $(selector, context);
		
		var createMarkItUp = function(elem){
			var q = 'module=' + escape(_this.module) + '&field=' + escape($(elem).attr('name'));
			var markitUpClass = $(elem).attr('className');
			if (markitUpClass.length){
				var previewPath = markitUpClass.split(' ');
				if (previewPath.length && previewPath[0] != 'no_editor'){
					q += '&preview=' + previewPath[previewPath.length - 1];
				}
			}
			myMarkItUpSettings.previewParserPath = _this.previewPath + '?' + q;
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

			var ckId = $(elem).attr('id');
			var sourceButton = '<a href="#" id="' + ckId + '_viewsource" class="btn_field editor_viewsource">' + _this.lang('btn_view_source') + '</a>';
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
					
					
					$('#' + ckId + '_viewsource').text(_this.lang('btn_view_editor'));
					
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
					$('#' + ckId + '_viewsource').text(_this.lang('btn_view_source'))
					
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
		
	},
	
	_initLinkedFields : function(context){
		var _this = this;
		
		// needed for enclosure
		var bindLinkedKeyup = function(slave, master, func){
			var slaveId = _this._getFieldId(slave, context);
			var masterId = _this._getFieldId(master, context);
			
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
					var url = _this.modulePath + '/process_linked';
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
			if ($('#' + _this._getFieldId(slave, context)).val() == ''){
				if (typeof(master) == 'string'){
					bindLinkedKeyup(slave, master, url_title);
				} else if (typeof(master) == 'object'){
					
					for (var o in master){
						var func = false;
						var funcName = master[o];
						var val = $('#' + _this._getFieldId(o, context)).val();
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
						bindLinkedKeyup(n, o, func);
						break; // stop after first one
					}
				}
			}
		}
		if (this.initObj.linked_fields || window['__FUEL_LINKED_FIELDS'] != undefined){
			var linked = (window['__FUEL_LINKED_FIELDS'] != undefined) ? window['__FUEL_LINKED_FIELDS'] : this.initObj.linked_fields;
			for(var n in linked){
				bindLinked(n, linked[n]);
			}
		}
	
	},
	
	_getFieldId : function(field, context){
		if ($('.__fuel_module__', context).size()){
			var val = $('.__fuel_module__', context).attr('id');
			var prefix = val.split('--')[0];
			return prefix + '--' + field;
		}
		return field;
	},
	
	_initViewPage : function(context){
		var _this = this;
		
		var resizeViewPageModal = function(){
			var half = Math.floor($('#viewpage_modal').width()/2);
			$('#viewpage_modal').css('marginLeft', -half +'px');
		}
		if (!$('#viewpage_modal').size()){
			$('body').append('<div id="viewpage_modal" class="jqmWindow"></div>');
			$('#viewpage_modal').jqm({modal:false,toTop:true});
		}
		$('.view_action').click(function(){
			$('#viewpage_modal').jqmShow();
			var page = $(this).attr('href');
			var iframe = '<div id="viewpage_btns"><a href="' + page + '" id="viewpage_new_page" class="viewpage_btn" target="_blank">' + _this.lang('viewpage_new_window') + '</a><a href="#" id="viewpage_close" class="viewpage_btn">' + _this.lang('viewpage_close') + '</a></div><iframe id="viewpage_iframe" src="' + page + '"></iframe>';
			$('#viewpage_modal').empty().append(iframe);
			
			$('#viewpage_close').click(function(){
				$('#viewpage_modal').jqmHide();
				return false;
			});
			resizeViewPageModal();
			return false;
		})
		
		$(window).resize(
			function(e){
				resizeViewPageModal();
			}
		);
		
	},
	
	editModule : function(url, callback){
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
							html = $.trim(html);
							if ($(html).is('error')){
								_this.displayError($form, html);
							} else if (callback){
								callback(html);
							}
						}
					});
					return false;
				});
				$('.delete', $modalContext).click(function(){
					$.removeChecksave();
					
					if (confirm(_this.lang('confirm_delete'))){
						$form.find('.__fuel_inline_action__').val('delete');
						$form.ajaxSubmit({
							success: function(html){
								html = $.trim(html);
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
				_this.initSpecialFields($modalContext);
			}
		}).jqmShow();
	},
	
	displayError : function($form, html){
		$form.find('.inline_errors').addClass('notification error ico_error').html(html).animate( { backgroundColor: '#ee6060'}, 1500);
	},
	
	sortList : function(col){

		// turn on ajax filtering but just for sorting
		var newOrder = ($('#order').val() == 'desc' || col != $('#col').val()) ? 'asc' : 'desc';
		$("#col").val(col);
		$("#order").val(newOrder);
		this.redrawTable();
	},
	
	treeCallback : function(_this){
		$('#tree_loader').hide();
		$("#tree>ul").treeview({
			persist: "cookie",
			collapsed: false,
			unique: false,
			cookieId: _this.module + '_tree'
		});
		if (!_this.treeLoaded) _this.treeLoaded = true;
		
		
	},
	
	tableCallback : function(_this){
		$('#table_loader').hide();
		_this.tableLoaded = true;
		var publishUnpublish = function(__this, publishOrUnpublish){
			var id = $(__this).parent().find('.toggle_' + publishOrUnpublish).attr('id').substr(14);
			var $form = $(__this).closest('form');
			var params = $form.formSerialize(true);
			params['id'] = id;
			params['published'] = ((publishOrUnpublish == 'publish') ? 'yes' : 'no')
			$.post(_this.modulePath + '/' + publishOrUnpublish + '/' + id, params, function(html){
				_this.redrawTable(true, false);
			});
			
		}
		
		$('#data_table .publish_text').parent().addClass('publish_col');
		
		// set up row clicks
		$("#data_table td[class^='col']").each(function(){
			$(".publish_action", this).click(function(e){
				if ($(this).parent().find('.toggle_publish').size() > 0){
					publishUnpublish(this, 'publish');
				} else if ($(this).parent().find('.toggle_unpublish').size() > 0){
					publishUnpublish(this, 'unpublish');
				}
				return false;

			});
			if ($(this).find('a').size() <= 0){
				$(this).click(function(e){
					if (!_this.rearrangeOn){
						var actions_col = $(this).parent().find('td.actions');
						if (actions_col)
						{
							window.location = $('a:first', actions_col[0]).attr('href');
						}
					}
					return false;

				});
			}
		});
		
		// setup rearranging precedence
		if ($('#precedence').val() != 1){
			$('#rearrange').hide();
		}
		if (_this.rearrangeOn){
			_this.rearrangeItems();
		}
		
	},
	
	redrawTree : function(){
		$('#tree_loader').show();
		this.submitForm('#form_table', '#tree', this.treeAjaxURL, true, this.treeCallback);
	},
	
	redrawTable : function(useAjax, useCache){
		if (useAjax !== false) useAjax = true;
		$('#table_loader').show();
		this.submitForm('#form_table', '#data_table_container', this.tableAjaxURL, useAjax, this.tableCallback, useCache);
	},
	
	submitForm : function(formId, loadId, path, useAjax, callback, useCache){
		var _this = this;
		if (useCache !== false) useCache = true;
		if (useAjax){
			var params = $(formId).formToArray(false);
			var cache_key = $(formId).formSerialize(true);
			if (this.cache.isCached(cache_key) && useCache){
				$(loadId).html(this.cache.get(cache_key));
				callback(_this);
			} else {
				$(loadId).load(path, params, function(html){
					callback(_this);
					_this.cache.add(cache_key, html); // cache data
				});
			}
		} else {
			$(formId).submit();
		}
	},
	
	deleteItem : function(){
		//this._submit();
	},
	
	rearrangeItems : function(){
		var _this = this;
		if ($('#precedence').val() == 1 && this.rearrangeOn){
			$('#data_table').tableDnD({
				serializeRegexp: /[^data_table_row]*$/,
				onDrop:function(e){
					if (_this.rearrangeOn){
						$('#col').val(_this.initObj.precedence_col);
						$('#order').val('asc');
						var params = {
							data: $('#data_table').tableDnDSerialize(),
							url: _this.precedenceAjaxURL,
							type: 'post',
							success: function(html){
								_this.redrawTable(true, false);
							}
						
						}
						$.ajax(params);
					}
				}
			});
			
			$("#data_table").addClass('rearrange');
			
		}
	},
	
	lang : function(key){
		return this.localized[key];
	}
	
	
});