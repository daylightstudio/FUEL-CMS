fuel.controller.LoginController = jqx.lib.BaseController.extend({
	
	init: function(initObj){
		fuel.controller.BaseFuelController.prototype.notifications.call(this);
		$('#user_name').focus();
		this._super(initObj);
	}
});