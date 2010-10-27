// jqx.load('plugin', 'date');

fuel.controller.BlockController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){

		// call parent
		fuel.controller.BaseFuelController.prototype.add_edit.call(this);
	
		$('#no_modal').click(function(){
			var path = jqx.config.fuelPath + '/blocks/import_view_cancel/';
			$.post(path, {id:$('#id').val(), name:$('#name').val() }, function(html){
				if (html == 'success'){
					$('#view_twin_notification').hide();
				}
			});
			$('.jqmWindow').jqm().jqmHide();
			return false;
		});
		
		$('#yes_modal').click(function(){
			var path = jqx.config.fuelPath + '/blocks/import_view/';
			$.post(path, {id:$('#id').val(), name:$('#name').val() }, function(html){
				if (html != 'error'){
					var id = '#view';
					if ($(id).exists())
					{
						$(id).val(html);
						$(id).addClass('change');
					}
					$('#warning_window').hide();
				} else {
					new jqx.Message('Error importing view file');
				}
			});
			$('.jqmWindow').jqm().jqmHide();
			return false;
		});
		
		
	}	
});