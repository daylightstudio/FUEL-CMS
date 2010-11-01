fuel.controller.AssetsController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	items : function(){

		// call parent
		fuel.controller.BaseFuelController.prototype.items.call(this);
		var _this = this;
		$('#group_id').change(function(e){
			$('#form_table').submit();
		});
	}
	
});