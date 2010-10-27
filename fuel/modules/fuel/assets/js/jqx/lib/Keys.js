jqx.lib.Keys = {

	DELETE : 8,
	BACKSPACE : 46,
	ENTER : 13,
	ARROW_LEFT : 37,
	ARROW_UP : 38,
	ARROW_DOWN : 39,
	ARROW_RIGHT :40,
	TAB : 9,
	isNumeric : function(evt){
		var key = evt.keyCode;
		if (key >= 48 && key <= 58) return true;
		return false;
	},

	isNumericKeyPad : function(evt){
		var key = evt.keyCode;
		if (key >= 96 && key <= 105) return true;
		return false;
	},
	
	getNumericKeyValue : function(evt){
		var key = evt.keyCode;
		if (this.isNumericKeyPad(evt)){
			key = key - 48;
		}
		return String.fromCharCode(key)
	},
	
	isAlpha : function(evt){
		var key = evt.keyCode;
		if (key >= 65 && key <= 90) return true;
		return false;
	},
	
	isAlphaNumeric : function(evt){
		return (this.isAlpha(evt) || this.isNumeric(evt))
	},
	
	isDate : function(evt, delimiter){
		var key = evt.keyCode;
		if (!delimiter) delimiter = [191, 111];
		if (this.isNumeric(evt) || this.isNumericKeyPad(evt)) return true;
		if (delimiter.constructor == Array) {
			for (var i = 0; i < delimiter.length; i++){
				if (delimiter[i] == key) return true;
			}
			return false;
		} else {
			return (key == delimiter);
		}
	},
	
	isEditingKey : function(evt){
		if (evt.keyCode == this.DELETE || evt.keyCode == this.BACKSPACE || evt.keyCode == this.ENTER || 
		evt.keyCode == this.TAB	|| (evt.keyCode >= this.ARROW_LEFT && evt.keyCode <= this.ARROW_RIGHT)){
			return true;
		}
		return false;
	}
	
}
