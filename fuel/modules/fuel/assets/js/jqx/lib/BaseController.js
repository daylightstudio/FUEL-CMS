jqx.lib.BaseController = Class.extend({
	initObj : null,
	
	init : function(initObj) {
		try { document.execCommand('BackgroundImageCache', false, true); } catch(e) {};
		this.initObj = initObj || {};
		this.errors = new Array();
		this.callMethod(initObj.method);
	},
	
	callMethod : function(method){
		if (!method && this.initObj.method) method = this.initObj.method;
		if (method) {
			this[method](this.initObj);
		}
	},
	
	go : function (url, win){
		if (!win) win = window;
		win['location'] = url;
	},
	
	hasErrors : function(){
		if (this.errors.length > 0) return true;
		return false;
	},
	
	addError : function(msg, key){
		if (!key) key = this.errors.length;
		this.errors[key] = msg;
	},
	
	getErrors : function(){
		return this.errors;
	},
	
	displayErrorMessage : function(){
		var msg = "";
		for (var i = 0; i < this.errors.length; i++){
			msg += this.errors[i] + "\n";
		}
		new jqx.Message(msg, 'error');
		this.resetErrors();
	},
	
	resetErrors : function(){
		this.errors = new Array();
	}
	
});