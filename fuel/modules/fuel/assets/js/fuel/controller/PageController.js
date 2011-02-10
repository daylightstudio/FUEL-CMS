// jqx.load('plugin', 'date');

fuel.controller.PageController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){
		var _this = this;
		// do this first so that the fillin is in the checksaved value
		$('#location').fillin(_this.localized.pages_default_location);

		fuel.controller.BaseFuelController.prototype.add_edit.call(this);

		$.changeChecksaveValue('location', _this.localized.pages_default_location);

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
				$('#navigation_label').live('keyup', function(){
					$('#navigation_label').val($('#vars--page_title').val());
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
						$('#main_content').scrollTo($(id), 800);
					}
					$('#view_twin_notification').hide();
				} else {
					new jqx.Message('Error importing view file');
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
		}
		
	}	
});