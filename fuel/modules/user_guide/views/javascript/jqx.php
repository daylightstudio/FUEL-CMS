<h1>jQX Framework</h1>
<p><a href="<?=site_url('fuel/modules/'.FUEL_FOLDER.'/assets/js/jqx/jqx.js')?>">jQX</a> is a small javascript MVC framework.
Some of the benefits of using JQX instead of simply including javascript files:</p>
<ul>
	<li>Isolate all module actions to a single javascript file which loads other plugins and library files</li>
	<li>Automatically creates path configuration variables for images, css etc.</li>
	<li>Includes simple caching object to cache information</li>
	<li>Provides basic inheritance</li>
</ul>

<pre class="brush:php">
jqx.load('plugin', 'date');

fuel.controller.MyModuleController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},
	
	myMethod : function(){
		
	}
	....
}
</pre>

<p class="important">For more examples, look inside the fuel modules <kbd>js/fuel/controller</kbd> folder</p>