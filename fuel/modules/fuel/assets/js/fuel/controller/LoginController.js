jqx.load('plugin', 'jquery-ui-1.8.4.custom.min');

fuel.controller.LoginController = jqx.lib.BaseController.extend({
	
	init: function(initObj){
		fuel.controller.BaseFuelController.prototype._notifications.call(this);
		$('#user_name').focus();
		this._super(initObj);
	}
});