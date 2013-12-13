// jqx.load('plugin', 'date');

fuel.controller.PageController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){
		var _this = this;
		// do this first so that the fillin is in the checksaved value
		fuel.controller.BaseFuelController.prototype.add_edit.call(this, false);
		//this._super(false); // sometimes causes JS error with checksave???... not sure what's going on there
		
		// correspond page title to navigation label for convenience
		var blurred = false;
		
		var bindFields = function(){
			
			if ($('#vars--page_title').size()){
				$('#navigation_label').keyup(function(){
					$('#vars--page_title').val($('#navigation_label').val());
				});
			}
		}
		
		var _this = this;

		var retreiveLayoutVarsCallback = function(){
			var context = $('#fuel_main_content_inner');
			_this.initSpecialFields(context);
			$('#layout_vars').trigger('varsLoaded')
			if (jqx.config.warnIfModified) $.checksave('#fuel_main_content');
		}

		
		
		$('#layout').change(function(e){
			$('#layout_vars .loader').show();
			var path = jqx.config.fuelPath + '/pages/layout_fields/' + $('#layout').val() + '/' + $('#id').val() + '/' + $('#language').val();
			$('#layout_vars').load(path, function(){
				retreiveLayoutVarsCallback();
			});
		});
		
		$('#language').change(function(e){
			$.changeChecksaveValue('#language', $(this).val());
			window.location = jqx.config.fuelPath + '/pages/edit/' +  $('#id').val() + '?lang=' + $('#language').val();
		})
		
		$('#view_twin_cancel').click(function(){
			var path = jqx.config.fuelPath + '/pages/import_view_cancel/';
			var params = $('#form').serialize();
			$.post(path, params, function(html){
				if (html == 'success'){
					$('#view_twin_notification').hide();
				}
			});

			$('.jqmOverlay').hide();
			return false;
		});
		
		$('#view_twin_import').click(function(){
			var path = jqx.config.fuelPath + '/pages/import_view/';
			var params = $('#form').serialize();
			$.post(path, params, function(html){
				if (html != 'error'){

					$('#layout_vars').html(html);

					var renderedLayout = $('#vars--__layout__').val();

					if (renderedLayout.length){
						$('#layout').val(renderedLayout);
					}

					retreiveLayoutVarsCallback();
					$('#view_twin_notification').hide();
				} else {
					new jqx.Message(_this.lang('error_importing_ajax'));
				}
			});
			$('.jqmOverlay').hide();
			return false;
		});
		
		// only change for those that already exist
		if ($('#id').val() && $('#id').val().length){
			$('#layout').change();
		} else {
			bindFields();
			var context = $('#fuel_main_content_inner');
			_this.initSpecialFields(context);
			$('#form').formBuilder().initialize();
			$('#layout_vars').trigger('varsLoaded');
		}


		// add ability to create new navigation inline
		$('#related_items li a').click(function(e){
			var url = $(this).attr('href');
			var html = '<iframe src="' + url +'" id="add_edit_inline_iframe" class="inline_iframe" frameborder="0" scrolling="no" style="border: none; height: 0px; width: 0px;"></iframe>';
			var label = '';
			var group = '';
			var iframeContext = null;
			var _this = this;
			var onCloseCallback = function(){
				if (label.length){
					var newLabel = label + ' (' + group + ')';
					$(_this).html(newLabel);
				}
			}

			$modal = fuel.modalWindow(html, 'inline_edit_modal', true, null, onCloseCallback);
		
			// bind listener here because iframe gets removed on close so we can't grab the id value on close
			$modal.find('iframe#add_edit_inline_iframe').bind('load', function(){
				var iframeContext = this.contentDocument;
				label = $('#label', iframeContext).val();
				group = $('#group_id option:selected', iframeContext).text();
			})
			return false;
		})
	

	},
	
	
	upload : function(){
		this.notifications();
		//this._initAddEditInline($('#form'));
	},

	select : function(){
		$urlSelect = $('#url_select');
		this._initFormTabs();
		$('#input').bind('click focus', function(){
			$(this).removeAttr('disabled');
			$('#url_select').attr('disabled', 'disabled');
			
		})

		$('#input').bind('blur', function(){
			$('#url_select').removeAttr('disabled');
		})


		$('#url_select').bind('click focus', function(){
			$(this).removeAttr('disabled');
			$('#input').attr('disabled', 'disabled');
		})

		$('#url_select').bind('blur', function(){
			$('#input').removeAttr('disabled');
		})

	}
		
});