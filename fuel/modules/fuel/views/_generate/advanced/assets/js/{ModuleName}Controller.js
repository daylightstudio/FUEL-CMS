// jqx.load('plugin', 'date');

{ModuleName}Controller = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	add_edit : function(){
		var _this = this;
		// do this first so that the fillin is in the checksaved value
		//fuel.controller.BaseFuelController.prototype.add_edit.call(this, false);
		this._super();
	}		
});