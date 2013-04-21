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
		this.inline = parseInt($('#fuel_inline').val());
		this.tableAjaxURL = this.modulePath + '/items/';
		if (this.inline != 0) this.tableAjaxURL += '/?inline=' + this.inline;
		this.treeAjaxURL = this.modulePath + '/items_tree/';
		this.precedenceAjaxURL = this.modulePath + '/items_precedence/';
		this.tableLoaded = false;
		this.rearrangeOn = false;
		this.leftMenuInited = false;
		this.formController = null;
//		this.previewPath = myMarkItUpSettings.previewParserPath;
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

	
	_submit : function(){
		$('#submit').click(function(){
			$('#form').submit();
			return false;
		});
	},
	
	setNotification : function(msg, type, cssClass){
		if (!cssClass){
			cssClass = '';
		}
		switch(type){
			case 'warn' : case 'warning':
				cssClass = 'ico_warn warning ' + cssClass;
				break;
			case 'error':
				cssClass = 'ico_error error ' + cssClass;
				break;
			case 'success': case 'saved':
				cssClass = 'ico_success success ' + cssClass;
				break;
		}
		
		var html = '<div class="' + cssClass +'">' + msg + '</div>';
		$('#fuel_notification').html(html);
		this.notifications();
	},
	
	notifications : function(){
		// flash any notifications
		var speed = 1500;
		$(".notification .success").stop(true, true).animate( { backgroundColor: '#dcffb8'}, speed);
		$(".notification .error").stop(true, true).animate( { backgroundColor: '#ee6060'}, speed);
		$(".notification .warning").stop(true, true).animate( { backgroundColor: '#ffff99'}, speed);
	},
	
	items : function(){
		var _this = this;
		this.treeLoaded = false;
		this.tableLoaded = false;
		this.rearrangeOn = false;
		
		this.notifications();
		$('#search_term').focus();
		$('#limit').change(function(e){
			$('#form').submit();
		});
		
		if ($('#tree').exists()){
			var itemViewsCookieId = 'fuel_' + _this.module + '_items';
			var itemViewsCookie = $.cookie(itemViewsCookieId);
			
			$('#toggle_tree').click(function(e){
				_this._toggleRearrangeBtn();

				$('#toggle_tree').parent().addClass('active');
				if ($('#rearrange').parent().hasClass('active')){
					$('#rearrange').click();
				}
				
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

				//_this._toggleRearrangeBtn(); // don't need this because it is called in the redrawTable

				$('#fuel_notification .rearrange').show();
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
		
		
		$('#form').submit(function(){
			$('#toggle_list').click();
		});
		
		$('#multi_delete').click(function(){
			$('#toggle_list').unbind('click');
			var deleteUrl = _this.modulePath + '/delete/';
			$('#form').attr('action', deleteUrl).attr('method', 'post').submit();
			return false;
		});
		
		$('.multi_delete').live('click', function(e){
			if ($('.multi_delete:checked').length){
				$('#multi_delete').parent().show();
			} else {
				$('#multi_delete').parent().hide();
			}
		});
		$('#multi_delete').parent().hide();
		
		$('#rearrange').live('click', function(e){
			if (!$('#toggle_list').parent().hasClass('active')){
				$('#toggle_list').click();
			}
			
			$(this).parent().toggleClass('active');
			if ($(this).parent().hasClass('active')){
				// sort the order of the column first
				$('#col').val(_this.initObj.precedence_col);
				$('#order').val('asc');
				_this.redrawTable(true, false);
				
				_this.rearrangeOn = true;
				_this.rearrangeItems();
				_this.setNotification(_this.lang('rearrange_on'), 'warning', 'rearrange');
				
			} else {
				_this.rearrangeOn = false;
				$("#data_table").removeClass('rearrange');
				$('#fuel_notification .rearrange').remove();
				
			}
			
			return false;
		});
		
		// automatically set selects to submit
		$('.more_filters select').change(function(e){
			$('#form').submit();
		});
		
		// automatically set selects to submit
		$('#export_data').click(function(e){
			$('#form').attr('action', _this.modulePath + '/export').attr('method', 'post').submit();
			return false;
		});
		
		// select all
		$('a.ico_select_all').toggle(function(e){
				e.preventDefault();
				if (!$('#toggle_list').parent().hasClass('active')){
					$('#toggle_list').click();
				}
				$('a.ico_select_all').html(fuel.lang('btn_deselect_all'));
				$('#multi_delete').parent().show();
				$('.multi_delete', '#fuel_main_content').attr('checked', true);
			},
			function(){
				if (!$('#toggle_list').parent().hasClass('active')){
					$('#toggle_list').click();
				}
				$('a.ico_select_all').html(fuel.lang('btn_select_all'));
				$('#multi_delete').parent().hide();
				$('.multi_delete', '#fuel_main_content').attr('checked', false);
			});
	},
	
	add_edit : function(initSpecFields){
		if (initSpecFields == null) initSpecFields = true;
		var _this = this;

		this.notifications();
		//this._submit();
		if (initSpecFields) this.initSpecialFields($('#fuel_main_content_inner'));
		this._initViewPage();
		
		$('.publish_action').click(function(e){
			$.removeChecksave();
			if ($('#published:checkbox').length > 0){
				$('#published:checkbox').attr('checked', true);
			} else if ($('#published').length > 0){
				$('#published').val('yes');
			} else {
				$('#published_yes').attr('checked', true);
			}
			$('#form').submit();
			return false;
		});
		
		$('.unpublish_action').click(function(e){
			$.removeChecksave();
			if ($('#published:checkbox').length > 0) {
				$('#published:checkbox').attr('checked', false);
			} else if ($('#published').length > 0){
				$('#published').val('no');
			} else {
				$('#published_no').attr('checked', true);
			}
			$('#form').submit();
			return false;
		});
		
		$('.activate_action').click(function(e){
			$.removeChecksave();
                        
			// Check if element is a checkbox
			if ($('#active:checkbox').length > 1){
				$('#active:checkbox').attr('checked', true);
			} else if ($('#active').length > 0){
				$('#active').val('yes');
			} else {
				$('#active_yes').attr('checked', true);
			}
			
			$('#form').submit();
			return false;
		});

		$('.deactivate_action').click(function(e){
			$.removeChecksave();
                        
			// Check if element is a checkbox
			if ($('#active:checkbox').length > 1) {
				$('#active:checkbox').attr('checked', false);
			} else if ($('#active').length > 0){
				$('#active').val('no');
			} else {
				$('#active_no').attr('checked', true);
			}
			$('#form').submit();
			return false;
		});
		
		$('.duplicate_action').click(function(e){
			$('#form').attr('action', _this.modulePath + '/duplicate').submit();
			return false;
		});
		
		$('.replace_action').click(function(e){
			var url = $(this).attr('href');
			html = '<iframe id="replace_iframe" src="' + url + '"></iframe>';
			$modal = fuel.modalWindow(html, 'replace_modal', true);
			
			$modal.find('iframe#replace_iframe').bind('load', function(){
				var iframeContext = this.contentDocument;
				var replacedId = $('#new_fuel_replace_id', iframeContext).val();
				
				$('#form', iframeContext).submit(function(){
					if (confirm(fuel.lang('replace_warning'))){
						return true;
					}
					return false;
				})
				
				if (replacedId && replacedId.length){
					$modal.jqmHide();
					var url =  _this.modulePath + '/edit/' + replacedId;
					top.window.location = url;
				}
			})
			return false;
		});
		
		$('.delete_action').click(function(e){
			$.removeChecksave();
		});
		
		$('.save, #form input[type="submit"]').live('click', function(e){
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
		
		
		$('#fuel_restore_version').change(function(e){
			$.removeChecksave();
			if ($(this).val() != ''){
				if (confirm('Restoring previous data will overwrite the currently saved data. Are you sure you want to continue?')){
					var url =  _this.modulePath + '/restore';
					if (_this.inline) url += '/?inline=' + _this.inline;
					$('#form').attr('action', url).submit();
				}
			}
		});
		
		$('#fuel_other_items').change(function(e){
			$.removeChecksave();
			if ($(this).val() != ''){
				var url =  _this.modulePath + '/edit/' + $(this).val();
				if (_this.inline) url += '/?inline=' + _this.inline;
				window.location = url;
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
		
		if (jqx.config.warnIfModified) $.checksave('#fuel_main_content');
	},
	
	initSpecialFields : function(context){
		var _this = this;
		this._initFormTabs(context);
		this._initFormCollapsible(context);
		this._initToolTips(context);
		$('input, textarea').placeholder();
		$('#form input:first', context).select();
	},
	
	_initToolTips : function(context){
		$('.tooltip', context).tooltip({
			delay: 0,
			showURL: false,
			id: '__fuel_tooltip__'
		});
		
	},
	
	_initFormTabs : function(context){
		if (!$('#fuel_form_tabs', context).length){

			var tabId = jqx.config.uriPath.replace(/[\/|:]/g, '_').substr(5); // remove fuel_
			var tabCookieSettings = {group: 'fuel_tabs', name: tabId, params: {path: jqx.config.cookieDefaultPath}}

			var tabs = '<div id="fuel_form_tabs" class="form_tabs"><ul>';
			
			// prevent nested fieldsets from showing up with not()
			$legends = $('fieldset.tab legend', context).not('fieldset.tab fieldset legend', context);
			$legends.each(function(i){
				if ($(this).parent().attr('id') != '') {
					$(this).parent().attr('id', 'fieldset' + i);
				}
				var id = ($(this).parent().attr('id'));
				var text = $(this).text();
				tabs += '<li><a href="#' + id + '">' + text + '</a></li>';
			});
			$legends.hide();
			tabs += '</ul><div class="clear"></div></div>';

			var startIndex = parseInt($.supercookie(tabCookieSettings.group, tabCookieSettings.name));
			if (!startIndex) startIndex = 0;
			tabs += '<input type="hidden" name="__fuel_selected_tab__" id="__fuel_selected_tab__" value="' + startIndex + '" />';
			$legends.filter(':first').parent().before(tabs);

			$('#form').trigger('fuel_form_tabs_loaded', [$('#fuel_form_tabs')] );

			$tabs = $('#fuel_form_tabs ul', context);
			$tabs.simpleTab({cookie: tabCookieSettings});
			
			var tabCallback = function(e, index, selected, content, settings){
				$('#__fuel_selected_tab__').val(index);
			}
			$tabs.bind('tabClicked', tabCallback);
			
		}
	},
	
	_initFormCollapsible : function(context){
		
		//var collapsibleCookieSettings = {group: 'collapse', name: 'collapse_' + jqx.config.uriPath.replace(/\//g, '_'), params: {path: jqx.config.basePath}}
		
		$legends = $('fieldset.collapsible legend', context);
		$legends.toggle(
			function(i){
				$(this).next().hide();
				return false;
			},
			function(i){
				$(this).next().show();
				return false;
			}
		);
	},
	
	_initViewPage : function(){

		var _this = this;
		
		var resizeViewPageModal = function(){
			var half = Math.floor($('#__FUEL_modal__').width()/2);
			$('#__FUEL_modal__').css('marginLeft', -half +'px');
		}
		$('.view_action').click(function(e){
			
			var url = $(this).attr('href');
			var html = '<a href="#" id="viewpage_close" class="modal_close">' + _this.lang('viewpage_close') + '</a>';
			html += '<div id="viewpage_btns"><a href="' + url + '" id="viewpage_new_page" class="viewpage_btn" target="_blank">' + _this.lang('viewpage_new_window') + '</a></div>';
			html += '<iframe id="viewpage_iframe" src="' + url + '"></iframe>';
			$modal = fuel.modalWindow(html, 'viewpage_modal', false);
			
			$('#viewpage_close').click(function(){
				$('#__FUEL_modal__').jqmHide();
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
	
	_toggleRearrangeBtn : function(){

		// remove the button if no precedence columns
		if (!$('#precedence').length)
		{
			$('.ico_precedence').parent().remove();
		}
		
		if ($('#precedence').val() != 1){
			$('#rearrange').parent().hide();
		} else {
			$('#rearrange').parent().show();
		}
	},
	
	displayError : function($form, html){
		$form.find('.inline_errors').addClass('notification error ico_error').html(html).animate( { backgroundColor: '#ee6060'}, 1500);
	},
	
	sortList : function(col){
		// turn on ajax filtering but just for sorting
		var newOrder = ($('#order').val() == 'desc' || col != $('#col').val()) ? 'asc' : 'desc';
		$("#col").val(col);
		$("#order").val(newOrder);
		$('#rearrange').parent().removeClass('active');
		$('#fuel_notification .rearrange').remove();
		this.rearrangeOn = false;
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
		
		// setup rearranging precedence
		$('#rearrange').parent().hide();
		
	},
	
	tableCallback : function(_this){
		$('#table_loader').hide();
		_this.tableLoaded = true;
		var toggleOnOff = function(__this, toggleStatus){
			var id = $(__this).parent().find('.toggle_' + toggleStatus).attr('id').substr(14);
			var field = $(__this).parent().find('.toggle_' + toggleStatus).attr('data-field');
			var $form = $(__this).closest('form');
			var params = $form.formSerialize(true);
			params['id'] = id;
			params['field'] = field;
			$.post(_this.modulePath + '/toggle_' + toggleStatus + '/' + id + '/' + field, params, function(html){
				_this.redrawTable(true, false);
			});
			
		}
		
		$('#data_table .publish_text').parent().addClass('publish_col');
		
		// set up row clicks
		$("#data_table td[class^='col']").each(function(){
			$(".publish_action", this).click(function(e){
				if ($(this).parent().find('.toggle_on').length > 0){
					toggleOnOff(this, 'on');
				} else if ($(this).parent().find('.toggle_off').length > 0){
					toggleOnOff(this, 'off');
				}
				return false;

			});
			if ($(this).find('a').length <= 0){
				$(this).click(function(e){
					if (!_this.rearrangeOn){
						var actionsCol = $(this).parent().find('td.actions');
						var firstLink = $('a:first', actionsCol[0]).attr('href');
						if (firstLink && firstLink){
							window.location = firstLink;
						}
					}
					return false;

				});
			}

			if (!$('#data_table td.actions a:first').length){
				$('tr.rowaction').removeClass('rowaction');
			}
		});
		
		// setup rearranging precedence
		_this._toggleRearrangeBtn();

		if (_this.rearrangeOn){
			_this.rearrangeItems();
		}
		
	},
	
	redrawTree : function(){
		$('#tree_loader').show();
		this.submitForm('#form', '#tree', this.treeAjaxURL, true, this.treeCallback);
	},
	
	redrawTable : function(useAjax, useCache){
		if (useAjax !== false) useAjax = true;
		$('#table_loader').show();
		this.submitForm('#form', '#data_table_container', this.tableAjaxURL, useAjax, this.tableCallback, useCache);
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
				//$(loadId).load(path, params, function(html){ // uses POST
				$.get(path, params, function(html){
					$(loadId).html(html);
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
						//$('#col').val(_this.initObj.precedence_col);
						//$('#order').val('asc');
						var $form = $('#form');
						var csrf = $('#csrf_test_name').val();
						var data = $('#data_table').tableDnDSerialize() + '&csrf_test_name='+ csrf;
						var params = {
							data: data,
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