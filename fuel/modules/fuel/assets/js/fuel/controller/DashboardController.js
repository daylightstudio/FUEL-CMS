fuel.controller.DashboardController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
		var refreshInterval = 1000 * 600; // every 10 minutes
		var loadModules = function()
		{
			$('.dashboard_module').each(function(i){
				var module = $(this).attr('id').replace('dashboard_', '').split('-').join('/');
				$(this).load(jqx.config.basePath + module + '/dashboard', function(){
					// console.log('loaded module ' + module);
				});
			});
		}

		loadModules();
		var interval = setInterval(loadModules, refreshInterval);
	}
	
});