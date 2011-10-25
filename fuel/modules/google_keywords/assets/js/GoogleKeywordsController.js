GoogleKeywordsController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init : function(initObj){
		this._super(initObj);
	},
	google_keywords : function(initObj){
		this._submit();
		this.notifications();
		var _this = this;
		var options = { 
			beforeSubmit: function(){
				$('#keyword_loader').show();
			},
			success: function(html){
				$('#results').html(html);
				$('#keyword_loader').hide();
			},
			error: function(html){
				$('#results').html(html);
				$('#keyword_loader').hide();
			}
			
		};
		$('#form').ajaxForm(options);
		$('#submit_google_keywords').click(function(){
			$('#form').submit();
			return false;
		})
		
	}
	
});