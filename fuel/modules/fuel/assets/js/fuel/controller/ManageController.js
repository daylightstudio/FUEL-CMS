fuel.controller.ManageController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this.notifications();
		this._submit();
		this._super(initObj);
	},
	
	activity: function(){
		this.tableAjaxURL = jqx.config.fuelPath + '/manage/activity';
		this.items();
	}
});