<h1>Javascript</h1>
<p>FUEL CMS provides several ways for you to insert javascript functionality for various aspects and scopes of the CMS which we list below. <a href="http://jquery.com" target="_blank">jQuery</a> 
is automatically loaded as are most of the <a href="http://jqueryui.com" target="_blank">jQuery UI</a> libraries for you to take advantage of.
A list of all the javascript files loaded in the admin by default can be found on the <a href="<?=user_guide_url('installation/configuration')?>">configuration</a> page.</p>


<h2>FUEL Configuration</h2>
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
needed for the field. This configuration is done in the <span class="file">fuel/modules/fuel/config/form_builder.php</span> file and can be done for your specific field types in the <span class="file">fuel/application/config/form_builder.php</span> file.</p>

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

<h2>AJAX with Models</h2>
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
Some of the benefits of using JQX instead of simply including javascript files:</p>
<ul>
	<li>Isolate all module actions to a single javascript file which loads other plugins and library files</li>
	<li>Automatically creates path configuration variables for images, css etc.</li>
	<li>Includes simple caching object to cache information</li>
	<li>Provides basic inheritance</li>
</ul>

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


