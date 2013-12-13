/*
(c) Copyrights 2011

Author David McReynolds
Daylight Studio
dave@thedaylightstudio.com
*/

;(function() {
	
	// Create a new copy of jQuery using sub()
	var plugin = jQuery.sub();

	// Extend that copy with the new plugin methods
	plugin.fn.extend({
		
		_funcs:{},
		
		_context:null,
		
		form: function(context) {
			if (context){
				this._context = context;
			} else {
				return this._context;
			}
		},
		
		add: function(key, name) {
			var func = (typeof(name) == 'string') ? eval(name) : name;
			this._funcs[key] = func;
		},

		call: function(key) {
			if (this._funcs[key] != undefined){
				if (this._funcs[key].func != undefined){
					var func = eval(this._funcs[key].func);
					var options = this._funcs[key].options;
					func(this.form(), options);
				} else {
					this._funcs[key](this.form());
				}
			}
		},
		
		initialize: function(context){
			if (!context){
				context = this;
			}
			this.form(context);
			for(var n in this._funcs){
				this.call(n);
			}
		}
	});
	
	// Add our plugin to the original jQuery
	jQuery.fn.formBuilder = function(options) {
		var p = plugin(this);

		// store functions to be called later
		var funcs = {};
		if (options != undefined){
			$.data(this, 'funcs', options);
			funcs = options;
		} else {
			funcs = $.data(this, 'funcs');
		}
		
		for(var o in funcs){
			var func = funcs[o];

			// add function to the object
			p.add(o, func);
		}
		
		return p;
	  };
	}
)();