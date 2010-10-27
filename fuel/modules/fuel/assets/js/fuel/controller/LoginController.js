fuel.controller.LoginController = jqx.lib.BaseController.extend({
	
	init: function(initObj){
//		this._notifications();
		$('#user_name').focus();
		this._super(initObj);
	}
});