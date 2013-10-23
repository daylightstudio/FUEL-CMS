<h1>Simple Modules</h1>
<p>Modules in FUEL can range from simple forms to a complex, interactive, multi-page interface. 
<strong>Simple</strong> modules don't require additional views and controllers to be created. 
Common examples of simple modules include events, news and job postings.</p>
<p>For more on creating a simple module, <a href="<?=user_guide_url('modules/tutorial')?>">view the user guide's tutorial</a>.</p>

<h2>Configuring</h2>
<p>A simple module usually contains a single model that maps to a database table (e.g. news). 
It is recommended that you create your model first (<a href="<?=user_guide_url('modules/tutorial#authors_model')?>">click here for an example</a>).
After your model has been created, you can activate and configure the module in the <dfn>config/MY_fuel_modules.php</dfn> file like so:</p>

<pre class="brush: php">
$config['modules']['example'] = array();
</pre>

<p>This will create a module that maps to the model named <dfn>example_model</dfn> and will have the friendly name of <dfn>Example</dfn>.</p>

<p>The above could actually be written a longer way as follows:</p>
<pre class="brush: php">
$config['modules']['example'] = array(
    'module_name' => 'Example',
    'module_uri' => 'example',
    'model_name' => 'example_model',
    'model_location' => '',
    'display_field' => 'name',
    'preview_path' => '',
    'permission' => 'example',
    'instructions' => 'Here you can manage the examples for your site.',
    'archivable' => TRUE,
    'nav_selected' => 'example'
);
</pre>

<h3>Additional Configuration Parameters</h3>

<p>Below is a list of all the parameters you can specify to further customize your module. Many of them base their default values
on the key value specified in the config (e.g. example):</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>module_name</strong></td>
			<td>The default value is a <a href="http://codeigniter.com/user_guide/helpers/inflector_helper.html" target="_blank">humanized</a> version of the module key (e.g. a key of example = Example)</td>
			<td>None</td>
			<td>the friendly name of the module.</td>
		</tr>
		<tr>
			<td><strong>module_uri</strong></td>
			<td>The default is the module key (e.g. example)</td>
			<td>None</td>
			<td>the URI path to the module that will appear after <dfn>fuel</dfn>.</td>
		</tr>
		<tr>
			<td><strong>model_name</strong></td>
			<td>The default is the module key with the suffix of <strong>_model</strong> (e.g. example_model)</td>
			<td>None</td>
			<td>the name of the model</td>
		</tr>
		<tr>
			<td><strong>model_location</strong></td>
			<td>By default it will look in the application/model folder. If a value is given it will look in the <dfn>modules/{model_location}/model</dfn> folder</td>
			<td>None</td>
			<td>The module folder location of the model.</td>
		</tr>
		<tr>
			<td><strong>view_location</strong></td>
			<td>By default it will look in the application/views folder. If a value is given it will look in the <dfn>modules/{view_location}/views</dfn> folder</td>
			<td>None</td>
			<td>the location of the view files</td>
		</tr>
		<tr>
			<td><strong>display_field</strong></td>
			<td>The default is the field <dfn>name</dfn>. If you do not have a field of <dfn>name</dfn>, you must specify a new field</td>
			<td>None</td>
			<td>The model field to be used for display purposes</td>
		</tr>
		<tr>
			<td><strong>js_controller</strong></td>
			<td>The default is BaseFuelController</td>
			<td>None</td>
			<td>The name of the javascript controller. If an array is given, then the key of the array is considered the module folder to look in and the value the name of the controller.
			The <dfn>js_controller_path</dfn> will automatically be changed to the module's assets folder if no <dfn>js_controller_path</dfn> is provided.
			For more information on creating javascript controllers, visit the section on the javascript <a href="<?=user_guide_url('general/javascript#jqx')?>">jqx Framework</a></td>
		</tr>
		<tr>
			<td><strong>js_controller_path</strong></td>
			<td>The default is the path to the fuel module's asset folder (e.g. fuel/modules/fuel/assets/js/</td>
			<td>None</td>
			<td>The base path of where to look for the controller file.</td>
		</tr>
		<tr>
			<td><strong>js_controller_params</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Parameters to pass to the jqx controller. They must be in the form of a json object</td>
		</tr>
		<tr>
			<td><strong>js</strong></td>
			<td>None</td>
			<td>None</td>
			<td>additional javascript files to include for the module</td>
		</tr>
		<tr>
			<td><strong>preview_path</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The URI path to preview the information from the module in the website</td>
		</tr>
		<tr>
			<td><strong>views</strong></td>
			<td>Built in FUEL pages</td>
			<td>None</td>
			<td>View files for the list, form and delete pages</td>
		</tr>
		<tr>
			<td><strong>permission</strong></td>
			<td>The default is the module key (e.g. <dfn>example</dfn>)</td>
			<td>None</td>
			<td>The name of the permission that users must be subscribed to to have access to the module.</td>
		</tr>
		<tr>
			<td><strong>edit_method</strong></td>
			<td>The default is <dfn>find_one_array</dfn> filtered on the id value passed to the form</td>
			<td>None</td>
			<td>The model's method to retrieve the information to edit.</td>
		</tr>
		<tr>
			<td><strong>instructions</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The instructions to provide users when creating or editing</td>
		</tr>
		<tr>
			<td><strong>filters</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Fields to filter the values in the list view</td>
		</tr>
		<tr>
			<td><strong>archivable</strong></td>
			<td>TRUE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>Saves to the archive table for later retrieval if you need to revert back</td>
		</tr>
		<tr>
			<td><strong>table_actions</strong></td>
			<td>EDIT, VIEW, DELETE</td>
			<td>None</td>
			<td>The actions per row to include in the table view </td>
		</tr>
		<tr>
			<td><strong>item_actions</strong></td>
			<td>save, view, publish, activate, delete, duplicate, create</td>
			<td>save, view, publish, activate, delete, duplicate, create, others</td>
			<td>The form level actions to include in the toolbar. 
				The <dfn>others</dfn> action is a key / value array with the key being the FUEL URI and the value being the button label.
				It can be used to create additional custom buttons. Each button will submit the form information on the page to the link path of the button.
			</td>
		</tr>
		<tr>
			<td><strong>list_actions</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Other HTML to be displayed at the top above the list table</td>
		</tr>
		<tr>
			<td><strong>rows_selectable</strong></td>
			<td>TRUE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>A boolean value that will set the whole row to be clickable</td>
		</tr>
		<tr>
			<td><strong>precedence_col</strong></td>
			<td>precedence</td>
			<td>None</td>
			<td>The name of the column to contain the custom precedence sorting information</td>
		</tr>
		<tr>
			<td><strong>clear_cache_on_save</strong></td>
			<td>TRUE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>Clears the cache upon saving new or updated values to a record</td>
		</tr>
		<tr>
			<td><strong>create_action_name</strong></td>
			<td>Create</td>
			<td>None</td>
			<td>The name of the button to be used for creating an item</td>
		</tr>
		<tr>
			<td><strong>configuration</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Additional configuration files to load</td>
		</tr>
		<tr>
			<td><strong>nav_selected</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The left navigation item to be selected</td>
		</tr>
		<tr>
			<td><strong>table_headers</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The columns to use for the table view</td>
		</tr>
		<tr>
			<td><strong>default_col</strong></td>
			<td>The default is the second column of the table (e.g. the one normally after the id column)</td>
			<td>None</td>
			<td>The default field to sort on</td>
		</tr>
		<tr>
			<td><strong>default_order</strong></td>
			<td>asc</td>
			<td>asc, desc</td>
			<td>The default field ordering</td>
		</tr>
		<tr>
			<td><strong>sanitize_input</strong></td>
			<td>TRUE</td>
			<td>Boolean TRUE/FALSE, string of "xss", "php", "template" OR "entities", OR an array for multiple values e.g. array('xss', 'php', 'template', 'entities'). See the FUEL <a href="<?=user_guide_url('general/configuration')?>">configuration</a> for changing the different sanitization callbacks.</td>
			<td>Cleans the input before inserting or updating the data source</td>
		</tr>
		<tr>
			<td><strong>sanitize_files</strong>  (was sanitize_images)</td>
			<td>TRUE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>uses xss_clean function on images</td>
		</tr>
		<tr>
			<td><strong>displayonly</strong></td>
			<td>None</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>Will only display the data and not allow you to save it</td>
		</tr>
		<tr>
			<td><strong>language</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Additional language files to load</td>
		</tr>
		<tr>
			<td><strong>language_col</strong></td>
			<td>language</td>
			<td>None</td>
			<td>The column name that contains the language key value</td>
		</tr>
		<tr>
			<td><strong>hidden</strong></td>
			<td>FALSE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>If set to TRUE, it will hide the module in the admin menu</td>
		</tr>
		<tr>
			<td><strong>icon_class</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The CSS icon class to associate with the module</td>
		</tr>
		<tr>
			<td><strong>folder</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The advanced module folder in which it lives in</td>
		</tr>
		<tr>
			<td><strong>exportable</strong></td>
			<td>FALSE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>Will display an "Export" button on the list view page that will allow users to export the currently filtered list data</td>
		</tr>
		<tr>
			<td><strong>limit_options</strong></td>
			<td>array('25' => '25', '50' => '50', '100' => '100')</td>
			<td>array</td>
			<td>The "Show" options displayed in the list view</td>
		</tr>
		<tr>
			<td><strong>advanced_search</strong></td>
			<td>FALSE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>If TRUE, this will display model filters in an advanced search menu</td>
		</tr>
		<tr>
			<td><strong>disable_heading_sort</strong></td>
			<td>FALSE</td>
			<td>Boolean Value TRUE/FALSE</td>
			<td>If TRUE, this will disable heading sorting on the list view</td>
		</tr>
		<tr>
			<td><strong>description</strong></td>
			<td>None</td>
			<td>None</td>
			<td>A description value that can be used in the site docs</td>
		</tr>
	</tbody>
</table>


<h2 id="model">The Model</h2>
<p>The module's model is the most important piece of a module. It is what drives the the saving, form and extra interactions needed for the module to work.
A module should extend the abstract <a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model</a> 
class which is a child of the MY_Model class and is found in the <dfn>modules/fuel/model</dfn> folder. 
Custom record object should extend the <a href="<?=user_guide_url('libraries/base_module_model#base_module_record')?>">Base_module_record</a>
<p>

<p>There are several methods that you can add/overwrite to give you greater functionality for your module (<a href="<?=user_guide_url('modules/tutorial')?>">view the tutorial for more information</a>).
</p>

<ul>
	<li>list_items()</li>
	<li>tree()</li>
	<li>form()</li>
</ul>

<p>Additionally, there are <a href="<?=user_guide_url('libraries/my_model#hooks')?>">several hooks</a> you may want to use that allow you to insert functionality during the saving and deleting process of a modules record.</p>

<h2 id="views">The Views</h2>
<p>Simple modules are made up of several views:</p>
<ul>
	<li><a href="<?=user_guide_url('introduction/interface#list_view')?>"><strong>List view</strong></a> - where you can filter and select from a list and edit, delete or preview.</li>
	<li><a href="<?=user_guide_url('introduction/interface#edit_view')?>"><strong>Form view</strong></a> - the form view that allows you to edit or input new records for the module</li>
	<li><strong>Tree view (optional)</strong> - provides a tree like hierarchical structure. Requires a tree method on the module's model that follows the hierarchical <dfn>Menu</dfn> structure</li>
	<li><strong>Preview view (optional)</strong> - the URO to the website to preview the module</li>
</ul>

<h2 id="overwrites">Module Overwrites</h2>
<p>Module overwrites allow for you to overwrite existing module parameters. For example, if you'd like to overwrite the built-in FUEL fuel_pages_model to incorporate your own model hook you've added to the MY_pages_model,
you can do so like so:</p>
<pre class="brush:php">
$config['module_overwrites']['pages']['model_name'] = 'MY_pages_model';
</pre>
