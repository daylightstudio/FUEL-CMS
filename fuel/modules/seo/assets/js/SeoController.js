var SeoController = {
	
	init : function(initObj){
		this._super(initObj);
	},
	google_keywords : function(initObj){
		this._submit();
		this._notifications();
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
		
	},
	
	page_analysis : function(initObj){
		this._submit();
		this._notifications();
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
	//	$('#form').ajaxForm(options);
		$('#submit_page_analysis').click(function(){
			$('#form').submit();
			return false;
		})
	}
	
	
};
jqx.extendController(SeoController);