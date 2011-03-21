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
						if (CKEDITOR.instances['view']){
							CKEDITOR.instances['view'].setData($('#view').val());
							var scrollTo = '#cke_' + 'view';
						} else {
							var scrollTo = id;
						}
						$('#main_content').scrollTo($(scrollTo), 800);
					}
					$('#warning_window').hide();
				} else {
					new jqx.Message('Error importing view file');
				}
			});
			$('.jqmWindow').jqm().jqmHide();
			return false;
		});
		
	},
	
	upload : function(){
		this._notifications();
		this._initAddEditInline($('#form'));
	}
		
});