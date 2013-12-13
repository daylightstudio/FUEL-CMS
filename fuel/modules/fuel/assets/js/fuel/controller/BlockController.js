// jqx.load('plugin', 'date');

fuel.controller.BlockController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){

		// call parent
		//fuel.controller.BaseFuelController.prototype.add_edit.call(this);
		this._super();
		
		$('#no_modal').click(function(){
			var path = jqx.config.fuelPath + '/blocks/import_view_cancel/';
			var params = $('#form').serialize();
			$.post(path, params, function(html){
				if (html == 'success'){
					$('#warning_window').hide();
				}
			});
			$('.jqmWindow').jqm().jqmHide();
			return false;
		});
		
		$('#yes_modal').click(function(){
			var path = jqx.config.fuelPath + '/blocks/import_view/';
			var params = $('#form').serialize();
			$.post(path, params, function(html){
				if (html != 'error'){
					var id = '#view';
					if ($(id).exists())
					{
						$(id).val(html);
						$(id).addClass('change');
						if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances['view']){
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
			
			//$('.jqmWindow').jqm().jqmHide(); // causes error because of multiple modals
			$('.jqmOverlay').hide();
			return false;
		});
		
	},
	
	upload : function(){
		this.notifications();
//		this._initAddEditInline($('#form'));
	}
		
});