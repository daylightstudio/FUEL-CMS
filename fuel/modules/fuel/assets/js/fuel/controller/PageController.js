// jqx.load('plugin', 'date');

fuel.controller.PageController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){
		var _this = this;
		// do this first so that the fillin is in the checksaved value
		$('#location').fillin(_this.localized.pages_default_location);

		fuel.controller.BaseFuelController.prototype.add_edit.call(this, false);

		// if new, then we use default fillin value... else set to actual value
		if ($('#id').val() == ''){
			$.changeChecksaveValue('location', _this.localized.pages_default_location);
		} else {
			$.changeChecksaveValue('location', $('#location').val());
		}

		// correspond page title to navigation label for convenience
		var blurred = false;
		
		var bindFields = function(){
			
			/* don't want to automatically fill out a navigation item if nav isn't being used
			if ($('#vars--page_title').size()){
				$('#navigation_label').live('keyup', function(){
					if (!blurred) $('#vars--page_title').val($('#navigation_label').val());
				}).blur(function(e){
					blurred = true;
				});
			}

			if ($('#vars--page_title').size()){
				$('#navigation_label').live('keyup', function(){
					if (!blurred) $('#navigation_label').val($('#vars--page_title').val());
				}).blur(function(e){
					blurred = true;
				});
			}*/
			
			if ($('#vars--page_title').size()){
				$('#navigation_label').keyup(function(){
					$('#vars--page_title').val($('#navigation_label').val());
				});
			}
		}
		
		var _this = this;
		$('#layout').change(function(e){
			var path = jqx.config.fuelPath + '/pages/layout_fields/' + $('#layout').val() + '/' + $('#id').val();
			$('#layout_vars').load(path, {}, function(){
				_this.initSpecialFields();
			});
		});
		
		$('#view_twin_cancel').click(function(){
			var path = jqx.config.fuelPath + '/pages/import_view_cancel/';
			var params = $('#form').serialize();
			$.post(path, params, function(html){
				if (html == 'success'){
					$('#view_twin_notification').hide();
				}
			});
			$('.jqmWindow').jqm().jqmHide();
			return false;
		});
		
		$('#view_twin_import').click(function(){
			var path = jqx.config.fuelPath + '/pages/import_view/';
			var params = $('#form').serialize();
			$.post(path, params, function(html){
				if (html != 'error'){
					var id = '#' + _this.initObj.import_view_key;
					if ($(id).exists())
					{
						$(id).val(html);
						$(id).addClass('change');
						if (CKEDITOR.instances[_this.initObj.import_view_key]){
							CKEDITOR.instances[_this.initObj.import_view_key].setData($(id).val());
							var scrollTo = '#cke_' + _this.initObj.import_view_key;
						} else {
							var scrollTo = id;
						}
						$('#main_content').scrollTo($(scrollTo), 800);
					}
					$('#view_twin_notification').hide();
				} else {
					new jqx.Message(_this.lang('error_importing_ajax'));
				}
			});
			$('.jqmWindow').jqm().jqmHide();
			return false;
		});
		
		// only change for those that already exist
		if ($('#id').val().length){
			$('#layout').change();
		} else {
			bindFields();
			_this.initSpecialFields();
		}
		
	},
	
	
	upload : function(){
		this._notifications();
		this._initAddEditInline($('#form'));
	}
		
});