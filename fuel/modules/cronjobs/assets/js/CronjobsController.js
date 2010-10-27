var CronjobsController = {
	
	init : function(initObj){
		this._notifications();
		this._submit();
		this._super(initObj);
		
	},
	
	cronjobs : function(){
		$('.fillin').fillin();
		$('#remove').click(function(e){
			$('#action').val('remove');
			$('#form').submit();
			return false;
		});
		
		$('.ico_remove_line').click(function(e){
			$(this).parent().find('input').val('');
			$('#form').submit();
			return false;
		});
	}
};
jqx.extendController(CronjobsController);