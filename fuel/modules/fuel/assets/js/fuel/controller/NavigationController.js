fuel.controller.NavigationController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	/*items : function(){
		// call parent
		fuel.controller.BaseFuelController.prototype.items.call(this);
		//fuel.controller.BaseFuelController.prototype.items();
		var _this = this;
		$('#group_id').change(function(e){
			$('#form_actions').submit();
		});
	},*/
	
	add_edit : function(){
		// call parent
		//fuel.controller.BaseFuelController.prototype.add_edit.call(this);
		this._super();
		//this._super.
		var origParentId = $('#parent_id').val();
		var id = $('#id').val();
		$('#group_id').change(function(e){
			var parentId = ($('#parent_id').val() != '') ? $('#parent_id').val() : origParentId;
			var path = jqx.config.fuelPath + '/navigation/parents/' + $('#group_id').val() + '/' + parentId + '/' + id;
			$('#parent_id').parent().load(path, {}, function(){
				$('#parent_id').val(parentId);
				$.changeChecksaveValue('#parent_id', origParentId);
			});
		});
	},
	
	upload : function(){
		this.notifications();
		//this._initAddEditInline($('#form'));
	}
	
	
});