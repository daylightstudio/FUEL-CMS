jQuery.noConflict();

// exposed fuel methods
if (fuel == undefined) var fuel = {};
(function($) {

	var initObj = __FUEL_INIT_PARAMS__;
	var pageId = initObj.pageId;
	var basePath = initObj.basePath;
	var imgPath = initObj.imgPath;
	var cssPath = initObj.cssPath;
	var jsPath = initObj.jsPath;
	var assetsImgPath = initObj.assetsImgPath;
	var assetsPath = initObj.assetsPath;
	var assetsAccept = initObj.assetsAccept;

	var markers = null;
	var X_OFFSET = 16;
	var Y_OFFSET = 16;

	var editorsOn = (parseInt($.supercookie('fuel_bar', 'show_editable_areas')) == 1);
	var fuelBarOn = (parseInt($.supercookie('fuel_bar', 'show_fuel_bar')) == 1);

	var activeField;
	var assetFolder;
	var iconHeight = 16;
	
	var loaderHTML = '';
	var maxAdjustLoops = (fuel.maxAdjustLoops) ? fuel.maxAdjustLoops : 10;
	
	
	// limit it to the most common for performance
	var useAutoAdjust = fuel.useAutoAdjust || true;
	var resizeTags = (fuel.resizeTags) ? fuel.resizeTags : 'div,p,li';

	jQuery.resize.delay = (fuel.resizeDelay) ? fuel.resizeDelay : 1000;
	
	$(document).ready(function(){
		function init(){
			initMarkers();
			initFUELBar();
			
			// bind exposed global methods
			fuel.refresh = function(){
				refresh();
			}
		}
		
		function initMarkers(){
			$('.__fuel_edit__').remove();
			var markers = $(".__fuel_marker__");
			var toggleEditOff = true;
			if (markers.size() > 0){
				$body = $('body');
				markers.each(function(i){
					var $this = $(this);
					if (($this.attr('data-module') == 'pages' && pageId != 0) || $this.attr('data-module') != 'pages'){
						$this.attr('id', '__fuel_marker__' + i);
						var coords = getMarkerPosition($this);
						var varName = $this.attr('title');
						var newClass = ($this.attr('data-rel') == 'create') ? ' __fuel_edit_marker_new__' : '';
						var html = '<div id="__fuel_edit__' + i + '" style="left:' + coords.x + 'px; top:' + coords.y + 'px;" class="__fuel__ __fuel_edit__" title="' + varName + '">';
						html += '<a href="' + $this.attr('data-href') + '" rel="' + $this.attr('data-rel') + '" class="__fuel_edit_marker__'+ newClass +'">';
						html += '<span class="__fuel_edit_marker_inner__">'+varName+'</span>';
						html += '</a>';
						html += '<div class="__fuel_edit_form__" style="display: none;"><img src="' + imgPath + 'spinner_sm.gif" width="16" height="16" alt="loading"></div>';
						html += '</div>';
						$body.append(html);
						toggleEditOff = false;
					}
				});
				$('.__fuel_edit_marker_inner__').hide();
				initEditors();
			}
			if (toggleEditOff) $('#__fuel_page_edit_toggle__').parent().hide();
		}
		
		function refresh(){
			if (editorsOn){
				moveMarkers();
			}
		}
		
		function moveMarkers(){
			var markers = $(".__fuel_marker__");
			markers.each(function(i){
				var $this = $(this);
				var coords = getMarkerPosition($this);
				
				$('#__fuel_edit__' + i).css({left: coords.x, top: coords.y});
				
				// determine if it is visible so that we can filter out the hidden to speed things up
				if ($this.filter(':hidden').size() != 0) {
					$('#__fuel_edit__' + i).hide();
				} else {
					$('#__fuel_edit__' + i).show();
				}
			});

			// re-adjust markers so they don't overlap
			var editors = $(".__fuel_edit__:visible");
			editors.each(function(i){
				adjustPosition(editors, $(this), 0);
			});

		}

		function getMarkerPosition(marker){
			var offset = marker.offset();
			var xCoord = offset.left;
			var yCoord = offset.top + iconHeight; // 16 is the icon height
			var x = (xCoord <= X_OFFSET) ? 0 : xCoord - X_OFFSET;
			var y = (yCoord <= Y_OFFSET) ? 0 : yCoord - Y_OFFSET;
			return {x:x, y:y};
		}
		
		// used to prevent overlaps of editors
		function adjustPosition(editors, $obj, counter){
			editors.each(function(i){
				var $compareObj = $(this);
				var topPos = parseInt($obj.css('top'));
				var leftPos = parseInt($obj.css('left'));
				
				var objAttrsId = $obj.attr('id');
				var objCompareAttrsId = $compareObj.attr('id');
				if (counter <= maxAdjustLoops && $obj.attr('id') != $compareObj.attr('id') && 
					Math.abs(topPos - parseInt($compareObj.css('top'))) < Y_OFFSET && 
					Math.abs(leftPos - parseInt($compareObj.css('left'))) < X_OFFSET){
					$compareObj.css('top', (topPos + Y_OFFSET) + 'px');
					counter++;
					adjustPosition(editors, $obj, counter);
					return false;
				}
			});
		}
		
		function initEditors(){
			
			var formAction = '';
			
			var editors = $('.__fuel_edit__');

			var activeEditor;
			var resetCss = {height: 'auto', width: 'auto', opacity: 1, display: 'block'};

			var closeEditor = function(){
				if (activeEditor){
					activeEditor.removeClass('__fuel_edit_active__');
					activeEditor.find('.__fuel_edit_marker_inner__, .__fuel_edit_form__').stop().css(resetCss).hide();
					activeEditor = null;
				}
			}
			
			var ajaxSubmit = function($form){
				$form.attr('action', formAction).ajaxSubmit(function(html){
					if ($(html).is('error')){
						var msg = $(html).html();
						if (msg != '' || msg != '1'){
							$form.find('.inline_errors').html(msg).animate( { backgroundColor: '#ee6060'}, 1500);
							$.scrollTo($form);
						}
					} else {
						closeEditor();
						window.location.reload(true);
					}
					return false;
				});
			}
			
			// set up cancel button
			$('.__fuel_edit__ .ico_cancel').live('click', function(){
				closeEditor();
				return false;
			});

			// set up save
			$('.__fuel_edit__ .ico_save').live('click', function(){
				$form = $(this).parents('.__fuel_edit_form__').find('form');
				ajaxSubmit($form);
				return false;
			});
			$('.__fuel_edit__ .delete').live('click', function(){
				if (confirm('Are you sure you want to delete this?')){
					$form = $(this).parents('.__fuel_edit_form__').find('form');
					$form.find('.__fuel_inline_action__').val('delete');
					ajaxSubmit($form);
				}
				return false;
			});
			
			
			$('body').append('<div id="__FUEL__asset_modal" class="__fuel__ __fuel_modal__ __fuel_edit_form__ jqmWindow"></div>');
			$('body').append('<div id="__FUEL__add_edit_modal" class="__fuel__  __fuel_modal__ __fuel_edit_form__ jqmWindow"></div>');
			
			editors.each(function(i){
				var $this = $(this);
				var _anchor = $('.__fuel_edit_marker__', this);

				_anchor.mouseover(function(){
					$('.__fuel_edit_marker_inner__', this).stop().css(resetCss).show();
				});

				_anchor.mouseout(function(){
					if ((activeEditor && activeEditor.attr('title') == $this.attr('title'))){
						return;
					} else {
						$('.__fuel_edit_marker_inner__', this).stop().css(resetCss).hide();
					}
				});

				_anchor.click(function(e){
					if (!activeEditor || activeEditor != $this){
						if (!loaderHTML.length){
							loaderHTML = $('.__fuel_edit_form__', $this).html();
						}
						if ($('.__fuel_edit_form__', $this).children().not('img').size() == 0){
							
							var relArr = $(this).attr('rel').split('|');
							var param1 = relArr[0];
							var param2 = (relArr.length >= 2) ? relArr[1] : pageId;
							
							formAction = $(this).attr('href') + param1 + '/' + param2;
							_anchor.next('.__fuel_edit_form__').load(formAction, function(){
								
								var context = $(this).parents('.__fuel_edit__').find('.__fuel_edit_form__')[0];
							
								$(this).show();
								
								// set up any date fields
								Date.format = 'mm/dd/yyyy';
								Date.firstDayOfWeek = 0;
								$('.datepicker', context).fillin('mm/dd/yyyy');
								$('.datepicker_hh', context).fillin('hh');
								$('.datepicker_mm', context).fillin('mm');
								//$('.datepicker').datePicker();
								var dpOptions = {startDate: '01/01/2000', endDate: '12/31/2100'}

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
								$('input:first', context).select();
								
								// set up tooltips
								$('.tooltip', context).tooltip({ delay: 0, showURL: false, id: '__fuel_tooltip__' });
								
								// set up markitup
								$markitupField = $('textarea:not(textarea[class=no_editor])', context);
								if ($markitupField.size()){
									
									var id = $this.attr('id').replace(/__fuel_edit__/, '');
									var module = $('#__fuel_marker__' + id).attr('data-module');
									var q = 'module=' + escape(module) + '&field=' + escape($markitupField.attr('name'));
									var markitUpClass = $markitupField.attr('className');
									if (markitUpClass.length){
										var previewPath = markitUpClass.split(' ');
										if (previewPath.length && previewPath[0] != 'no_editor'){
											q += '&preview=' + previewPath[previewPath.length - 1];
										}
									}
									myMarkItUpSettings.previewParserPath = myMarkItUpSettings.previewParserPath + '?' + q;
									$markitupField.markItUp(myMarkItUpSettings);
								}


								// set up supercomboselects
								$('select[multiple]', context).each(function(i){
									var comboOpts = {};
									var sortingId = 'sorting_' + $(this).attr('id');
									if ($('#' + sortingId).size()){
										comboOpts.autoSort = false;
										comboOpts.isSortable = true;
										comboOpts.selectedOrdering = eval(unescape($('#' + sortingId).val()));
									}
									$(this).supercomboselect(comboOpts);
								});

								
								// set up assets folder
								var _this = this;
								$('.asset_select', context).each(function(i){
									var assetTypeClasses = $(this).attr('className').split(' ');
									var assetFolder = (assetTypeClasses.length > 1) ? assetTypeClasses[1] : 'images';
									var btnLabel = '';
									switch(assetFolder.split('/')[0].toLowerCase()){
										case 'pdf':
											btnLabel = 'PDF';
											break;
										case 'images': case 'img': case '_img':
											btnLabel = 'Image';
											break;
										case 'swf': case 'flash':
											btnLabel = 'Flash';
											break;
										default :
											btnLabel = 'Asset';
									}
									$(this).after('&nbsp;<a href="'+ __FUEL_PATH__ + '/assets/select_ajax/' + assetFolder + '" class="btn_field asset_select_button ' + assetFolder + '">Select ' + btnLabel + '</a>');
								});
								
								$('.asset_select_button', context).click(function(e){
									_this.activeField = $(e.target).prev().attr('id');
									var assetTypeClasses = $(e.target).attr('className').split(' ');

									activeField = $(e.target).prev().attr('id');
									var assetTypeClasses = $(e.target).attr('className').split(' ');
									assetFolder = (assetTypeClasses.length > 0) ? assetTypeClasses[(assetTypeClasses.length - 1)] : 'images';
									$('#__FUEL__asset_modal').jqm({
										ajax: __FUEL_PATH__ + '/assets/select_ajax/' + assetFolder,
									 	onLoad: function(){
											$('#asset_select').val($('#' + activeField).val());
											if (!$('#asset_select').val()) $('#asset_select').val($('#asset_select').children(':first').attr('value'));
											var isImg = $('#asset_select').val().match(/\.jpg$|\.jpeg$|\.gif$|\.png$/);
											//if (assetFolder == 'images'){
											if (isImg){
												$('#asset_select').change(function(e){
													$('#asset_preview').html('<img src="' + assetsPath + assetFolder + '/' + $('#asset_select').val() + '" />');
												})
												$('#asset_select').change();
											} else {
												$('#asset_preview').hide();
											}

											$('.ico_yes', this).click(function(){
												$('#__FUEL__asset_modal').jqmHide();
												$('#' + activeField).val($('#asset_select').val());
												return false;
											});
											$('.ico_no', this).click(function(){
												$('#__FUEL__asset_modal').jqmHide();
												return false;
											});

										}
									}).jqmShow();
									return false;
								});
								
								
								// set up add/edit
								$('.add_edit', context).each(function(i){
									var $field = $(this);
									var fieldName = $field.attr('id').split('--')[1];
									var fieldId = $field.attr('id');
									var className = $field.attr('className').split(' ');
									var module = '';
									
									var $modalContext = $('#__FUEL__add_edit_modal');
									
									
									if (className.length > 1){
										module = className[1];
									} else {
										module = fieldName.substr(0, fieldName.length - 3) + 's'; // eg id = client_id so module would be clients
									}
									var url =__FUEL_PATH__ + '/' + module + '/inline_edit/';
									$field.after('&nbsp;<a href="' + url + 'create" class="btn_field add_inline_button">Add</a>');
									$field.after('&nbsp;<a href="' + url + $field.val() + '" class="btn_field edit_inline_button">Edit</a>');

									var refreshField = function(html){
										var refreshUrl = jqx.config.fuelPath + '/' + _this.module + '/refresh_field';
										var params = { field:fieldId, field_id: fieldId, values: $field.val(), selected:html};
										$.post(refreshUrl, params, function(html){
											$('#notification').html('<ul class="success ico_success"><li>Successfully added to module ' + module + '</li></ul>')
											_this._notifications();
											$modalContext.jqmHide();
											$('#' + fieldId).replaceWith(html);
											if ($('#' + fieldId).hasClass('combo'))
											{
												$('#' + fieldId).supercomboselect();
											}
										});
										$('#' + fieldId).change(function(){
											changeField($(this));
										});
										changeField($('#' + fieldId));
									}
									
									var displayError = function($form, html){
										$form.find('.inline_errors').html(html).animate( { backgroundColor: '#ee6060'}, 1500);
									}
									
									var changeField = function($this){
										if (($this.val() == '' || $this.attr('multiple')) || $this.find('option').size() == 0){
											if ($this.find('option').size() == 0){
												$this.hide();
											}
											$this.next().hide();
										} else {
											$this.next().show();
										}	
									}
									
									var editModule = function(url){
									//	var $modalContext =$('#__FUEL__add_edit_modal');
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
													$form.ajaxSubmit({
														success: function(html){
															if ($(html).is('error')){
																displayError($form, html);
															} else {
																refreshField(html);
															}
														}
													});
													return false;
												});
												
												$('.delete', $modalContext).click(function(){
													if (confirm('Are you sure you want to delete this?')){
														$form.find('.__fuel_inline_action__').val('delete');
														$form.ajaxSubmit({
															success: function(html){
																if ($(html).is('error')){
																	displayError($form, html);
																} else {
																	refreshField(html);
																}
															}
														});
													}
													return false;
												});
											}
										}).jqmShow();
									}

									var $modalContext = $('#__FUEL__add_edit_modal');

									$('.add_inline_button', context).click(function(e){
										editModule($(this).attr('href'));
										return false;
									});

									$('.edit_inline_button', context).click(function(e){
										editModule(url + $(this).prev().val());
										return false;
									});

									$field.change(function(){
										changeField($(this));
									});
									changeField($field);
								});
								
								// setup multi-file naming convention
								$.fn.MultiFile.options.accept = assetsAccept;
								$('.multifile:file', context).MultiFile({ namePattern: '$name___$i' }); 
								
								
								// now center it after all the above
								var curY = parseInt($(this).parent().css('top'));
								var curX = parseInt($(this).parent().css('left'));
								editorHeight = $(this).height();
								editorWidth = $(this).width();
								var y = curY + editorHeight;
								var x = curX + editorWidth;

								if ((curY + editorHeight) > $(window).height()){
									 $.scrollTo(curY, {axis: 'y'});
								}
								if ((curX + editorWidth) > $(window).width()){
									$.scrollTo(curX, {axis: 'x'});
								}
								

							});
						} else { 
							_anchor.next('.__fuel_edit_form__').show();
						}
						$('.__fuel_edit_marker_inner__', this).css(resetCss);
						$(this).find('.__fuel_edit_marker_inner__, .__fuel_edit_form__').show();

						$this.addClass('__fuel_edit_active__');

						if (activeEditor && (activeEditor.attr('title') != $this.attr('title'))) {
							closeEditor();
						}
						activeEditor = $this;
					} else {
						closeEditor();
					}
					return false;

				});
			});
		}
		
		function initFUELBar(){

			var hideEditors = function(){
				if (useAutoAdjust) $(resizeTags).unbind('resize', refresh);
				
				var elem = $('#__fuel_page_edit_toggle__');
				$('.__fuel_edit__').hide();
				editorsOn = false;

				//elem.text('Show Editable Areas');
				elem.parent('li').removeClass('active');
				$.supercookie('fuel_bar', 'show_editable_areas', '0', {path: basePath});
			}

			var showEditors = function(){
				// use the great resize plugin to accomplish this... 
				if (useAutoAdjust) $(resizeTags).bind('resize', refresh);
				refresh(); // just in case things have moved since they were last turned off
				var elem = $('#__fuel_page_edit_toggle__');
				$('.__fuel_edit__').show();
				
				editorsOn = true;
				//elem.text('Hide Editable Areas');
				elem.parent('li').addClass('active');
				$.supercookie('fuel_bar', 'show_editable_areas', '1', {path: basePath});
			}
			
			var toggleEditors = function(shown){
				if (shown){
					hideEditors();
				} else {
					showEditors();
				}
			}
			
			$('#__fuel_page_edit_toggle__').click(
				function(){
					toggleEditors(editorsOn);
					return false;
				}
			);

			$('#__fuel_page_layout__').change(function(){
				$('#__fuel_edit_bar_form__').ajaxSubmit(function(){
					window.location.reload();
				});
				return false;
			});

			$('#__fuel_page_publish_toggle__').click(function(e){
				var $this = this;
				var elem = $('#__fuel_page_published__')
				var val = (elem.val() == 'yes') ? 'no' : 'yes';
				elem.val(val);
				$('#__fuel_edit_bar_form__').ajaxSubmit(function(){
					window.location.reload();
				});
				return false;
			});

			$('#__fuel_page_cache_toggle__').click(function(e){
				var elem = $('#__fuel_page_cached__')
				var val = (elem.val() == 'yes') ? 'no' : 'yes';
				elem.val(val);
				$('#__fuel_edit_bar_form__').ajaxSubmit(function(){
					window.location.reload();
				});
				return false;
			});

			$('#__fuel_page_others__').change(function(){
				window.location = basePath + $(this).val();
			});

			var hideFuelBar = function(animate){
				var elem = $('#__fuel_page_toolbar_toggle__');
				var exposedWidth = 0;
				$('.__fuel__ .exposed').each(function(i){
					exposedWidth += $(this).innerWidth();
				});
				var barHideX = $('#__fuel_edit_bar__').width() - (exposedWidth + 1);
				if (animate){
					$("#__fuel_edit_bar__").animate({ right: '-' + barHideX + 'px'}, 500);
				} else {
					$("#__fuel_edit_bar__").css({ right: '-' + barHideX + 'px'});
				}

				fuelBarOn = false;
				elem.parent('li').removeClass('active');
				$.supercookie('fuel_bar', 'show_fuel_bar', '0', {path: basePath});
			}

			var showFuelBar = function(animate){
				var elem = $('#__fuel_page_toolbar_toggle__');
				if (animate){
					$("#__fuel_edit_bar__").show().animate({ right: '0px'}, 500);
				} else {
					$("#__fuel_edit_bar__").show().css({ right: '0px'});
				}
				$('.__fuel_edit_bar__').width();
				fuelBarOn = true;
				elem.parent('li').addClass('active');
				$.supercookie('fuel_bar', 'show_fuel_bar', '1', {path: basePath});
			}
			$('#__fuel_page_toolbar_toggle__').click(
				function(){
					toggleFuelBar(fuelBarOn, true);
					return false;
				}
			);
			
			var toggleFuelBar = function(shown, animate){
				if (shown){
					hideFuelBar(animate);
				} else {
					showFuelBar(animate);
				}
			}
			 // change to negative so it will toggle correctly
			$("#__fuel_edit_bar__").show();
			toggleFuelBar(!fuelBarOn, false);
			toggleEditors(!editorsOn, false);
		}
		
		init();
	});
})(jQuery);