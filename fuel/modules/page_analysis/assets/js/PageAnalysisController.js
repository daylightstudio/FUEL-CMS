PageAnalysisController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init : function(initObj){
		this._super(initObj);
	},
	
	page_analysis : function(initObj){
		this._submit();
		this.notifications();
		var _this = this;
		$('#submit_page_analysis').click(function(){
			$('#form').submit();
			return false;
		})
	}
});