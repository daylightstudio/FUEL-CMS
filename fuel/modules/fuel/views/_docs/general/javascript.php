<h1>Javascript</h1>
<p>FUEL CMS provides several ways for you to insert javascript functionality for various aspects of the CMS. <a href="http://jquery.com" target="_blank">jQuery</a> 
is automatically loaded as are most of the <a href="http://jqueryui.com" target="_blank">jQuery UI</a> libraries for you to take advantage of.
A list of all the javascript files loaded in the admin by default can be found on the <a href="<?=user_guide_url('installation/configuration')?>">configuration</a> page.
The following are topics which discuss adding javascript to FUEL CMS:
</p>

<ul>
	<li><a href="#config">FUEL Configuration</a></li>
	<li><a href="#modules">Modules</a></li>
	<li><a href="#forms">Forms</a></li>
	<li><a href="#ajax">AJAX with Models</a></li>
	<li><a href="#jqx">jQX Framework</a></li>
</ul>

<h2 id="config">FUEL Configuration</h2>
<p>If you'd like to include a javascript file for every page in the CMS, you can add it like so in your <span class="file">fuel/application/config/MY_fuel.php</span> file:</p>
<pre class="brush:php">
...
$config['fuel_javascript'][] = array('my_js');
...
</pre>
<p class="important">The javascript file's path will be relative to your <span class="file">assets/js/</span> folder.</p>
<p class="important">The <a href="<?=user_guide_url('assets/asset_helper')?>">asset helpers</a> <dfn>js()</dfn> function is used for rendering the javascript which means the ending ".js" is not required.</p>


<h2 id="modules">Modules</h2>
<p>To add javascript for an entire module, you can add a 'js' parameter to your module's initialization parameters in your <span class="file">fuel/application/config/MY_fuel_modules.php</span> like so:</p>
<pre class="brush:php">
$config['modules']['my_module'] = array(
	'js' => array('my_js')
);
</pre>

<h2 id="forms">Forms</h2>
<p>FUEL CMS 1.0 offers several new ways to incorporate javascript specific to your forms using the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class:</p>

<h3>add_js() Method</h3>
<p>You can use the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> class add_js() method like so:</p>

<pre class="brush:php">
$this->form_builder->add_js('my_js');
</pre>

<h3>Using the 'js' Parameter</h3>
<p>You can add the 'js' parameter to your fields like so:</p>
<pre class="brush:php">
...
$fields['my_field'] = array('label' => 'my_field', 'js' => 'my_js');
$this->form_builder->set_fields($fields);
$this->form_builder->render();
...

// OR you can define your script tag here
$fields['my_field'] = array('label' => 'my_field', 'js' => '&lt;script&gt;
$(function(){
	// my amazing javascript goes here
})
&lt;/script&gt;');
$this->form_builder->set_fields($fields);
$this->form_builder->render();

</pre>

<h3>Custom Field</h3>
<p>One of the most powerful new features of FUEL 1.0 is the addition of custom form fields with <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a>. A custom field is a combination of custom 
has 2 main parts. The first being a method or function that renders the field type. FUEL comes with several built in custom field types which are rendered using the <span class="file">fuel/modules/fuel/libraries/Fuel_custom_fields.php</span> file.
The second component is a configuration that associates the field type to that method or function and any javascript files and/or functions
needed for the field. This configuration is done in the <span class="file">fuel/modules/fuel/config/custom_fields.php</span> file and can be done for your specific field types in the <span class="file">fuel/application/config/custom_fields.php</span> file.</p>

<p>Custom fields have 5 javascript related parameters:</p>
<ul>
	<li><strong>js</strong>: the name of javascript file(s) to load</li>
	<li><strong>js_class</strong>: the CSS class used by the javascript to execute any javascript on the field</li>
	<li><strong>js_params</strong>: parameters to pass to the javascript function</li>
	<li><strong>js_exec_order</strong>: the order in which the javascript should be executed in relation to other fields... lower the sooner</li>
	<li><strong>js_function</strong>: the name of the javascript function to execute for the form field</li>
</ul>
<p>FUEL comes with several built in custom field types which can be viewed in the <span class="file">fuel/modules/fuel/libraries/Fuel_custom_fields.php</span> file and are configured with <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a> in the
<span class="file">fuel/modules/fuel/config/form_builder.php</span> file.</p>

<h2 id="ajax">AJAX with Models</h2>
<p>Sometimes you may want to use AJAX in your module's form. For example, you may have a list of cities that changes based on a state you select. 
To do this you can setup a method on your model that is prefixed with 'ajax_' (e.g. ajax_cities_by_state). Then use the following URL in your AJAX requests:</p>
<pre class="brush:php">
// $states is previously defined
$fields['state'] = array('label' => 'state', 'type' => 'select', 'options' => $states, 'first_option' => 'Select a state...', 'js' => '&lt;script&gt;
$(function(){
	$("#state").change(function(e){
		$.get('.fuel_url('my_module/ajax/cities_by_state').', function(data){
			$("#city").empty().append(data);
		})
	})
})
&lt;/script&gt;');
$this->form_builder->set_fields($fields);
$this->form_builder->render();
</pre>
<p class="important">Addtional parameters for the AJAX method can be passed via query pamareters (e.g. ?state=OR)</p>


<h2 id="jqx">jQX Framework</h2>
<p><a href="<?=site_url('fuel/modules/'.FUEL_FOLDER.'/assets/js/jqx/jqx.js')?>">jQX</a> is a small javascript MVC framework.
The following are some of the benefits of using JQX instead of simply including javascript files:</p>
<ul>
	<li>Isolate all module actions to a single javascript file which loads other plugins and library files</li>
	<li>Automatically creates path configuration variables for images, css etc.</li>
	<li>Includes simple caching object to cache information</li>
	<li>Provides basic inheritance and allows you to access parent methods using <dfn>this.super()</dfn></li>
</ul>

<h3>jQX Module Initialization Parameters</h3>
<p>There are additional module initialization parameters you can set in your <span class="file">fuel/application/confg/MY_fuel_modules.php</span>.if you are using a jQX controller which include:</p>
<ul>
	<li><strong>js_controller</strong>: the name of the file and thus controller to use. The default is the BaseFuelController.js</li>
	<li><strong>js_controller_path</strong>: the object/folder path to the controller. The default is <dfn>fuel.controller.BaseFuelController</dfn> (folder slashes are simply replaced with dots)</li>
	<li><strong>js_controller_params</strong>: an initialization object of parameters to pass to the controller.</li>
	<li><strong>js_localized</strong>: an array of localized string keys to be accessible via the <dfn>fuel.lang()</dfn> function</li>
</ul>

<p>The initialization parameter <dfn>method</dfn> has special meaning and should be the name of a method on your controller (e.g. myMethod).
Additionally, you can access the controller object after the window has been completely loaded (so $(window).load... not $().ready()) through the <dfn>window.page</dfn> variable.
</p>
<pre class="brush:php">
jqx.load('plugin', 'date');

controller.MyModuleController = jqx.createController(fuel.controller.BaseFuelController, {
	
	init: function(initObj){
		this._super(initObj);
	},

	items : function(){
		this._super();
	},

	add_edit : function(){
		this._super();
	},
	
	myMethod : function(){
		
	}
	....
}
</pre>
<p>The above example would assume that you have a jQX Controller in your advanced modules <span class="file">fuel/{module}/assets/js/controller</span> folder named MyModuleController. 
If the folder you wanted to save it in was <span class="file">fuel/{module}/assets/js/myfolder</span>, you would need to change the objects name to <dfn>myfolder.MyModuleController</dfn>. 
Essentially, you can swap out the folder "/" with object ".".</p>

<h3>Common Controller Methods</h3>
<p>There are two main jQX controller methods that you may want to override in your controller:</p>
<ul>
	<li><strong>items:</strong> the jQX controller method used for the list view of the module</li>
	<li><strong>add_edit:</strong> the jQX controller method used for adding/editing module data (the form view)</li>
</ul>
<p class="important">It's important to call use <dfn>this.super()</dfn> to call the parent method if overwriting.</p>

<h3>jQX Configuration Parameters</h3>
<p>The following jqx config parameters are available for you to use in your jQX controller:</p>
<ul>
	<li><strong>jqx.config.basePath:</strong> The equivalent value to the <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">site_url()</a> CI function </li>
	<li><strong>jqx.config.jsPath:</strong> The path to the fuel modoules javascript folder which is <span class="file">/fuel/modules/fuel/assets/js/</span></li>
	<li><strong>jqx.config.imgPath:</strong> The path to the fuel modoules images folder which is <span class="file">/fuel/modules/fuel/assets/images/</span></li>
	<li><strong>jqx.config.uriPath:</strong> The equivalent to the <a href="<?=user_guide_url('helpers/my_url_helper')?>">uri_path()</a> function</li>
	<li><strong>jqx.config.assetsImgPath:</strong> The path the sites images (e.g. <span class="file">/assets/images/</span>)</li>
	<li><strong>jqx.config.assetsPath:</strong> The path the sites images (e.g. <span class="file">/assets/</span>)</li>
	<li><strong>jqx.config.assetsCssPath:</strong> The path the sites images (e.g. <span class="file">/assets/css/</span>)</li>
	<li><strong>jqx.config.controllerName:</strong> The global name of the controller object which can be used by other scripts after the page is loaded. The default is "fuel"</li>
	<li><strong>jqx.config.jqxPath:</strong> The path to the jQX library which is <span class="file">/fuel/modules/fuel/assets/js/jqx</span></li>
	<li><strong>jqx.config.controllerPath:</strong> The path to the controller</li>
	<li><strong>jqx.config.pluginPath: The path to the jQuery plugins</strong></li>
	<li><strong>jqx.config.fuelPath:</strong> The path to the FUEL CMS which by default is <span class="file">fuel/</span></li>
	<li><strong>jqx.config.modulePath:</strong> The path to the module which is <span class="file">fuel/{module}</span></li>
	<li><strong>jqx.config.cookieDefaultPath:</strong> The server folder path used when assigning cookies</li>
	<li><strong>jqx.config.keyboardShortcuts:</strong> The keyboard shortcuts to be used in the CMS</li>
	<li><strong>jqx.config.warnIfModified:</strong> Determines whether to warn upon leaving a page that has unsaved field content</li>
	<li><strong>jqx.config.cacheString:</strong> A string value that represents the assets <dfn>last_updated</dfn> configuration value and can be used to caching or breaking a cache</li>
	<li><strong>jqx.config.assetsAccept:</strong> The asset files that are allowed to be updated</li>
	<li><strong>jqx.config.localized:</strong> A JSON object of available localized strings</li>
	<li><strong>jqx.config.editor:</strong> The text editor selected (either CKEditor or markItUp!)</li>
	<li><strong>jqx.config.ckeditorConfig:</strong> CKEditor configuration values</li>
</ul>  


<p class="important">For more examples, look inside the fuel modules <kbd>js/fuel/controller</kbd> folder.</p>


