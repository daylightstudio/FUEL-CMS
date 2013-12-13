<h1>Extending FUEL CMS</h1>
<p>FUEL CMS provides a number of ways for you to expand and customize it's functionality. This page just provides a consolodation of the most common ways:</p>

<ul>
	<li><a href="<?=user_guide_url('libraries/my_model')?>">Model Hooks</a>: Create custom callbacks after inserting, updating, saving (inserting or updating) and deleting model data</li>
	<li><a href="<?=user_guide_url('modules/hooks')?>">Module Hooks</a>: Create callbacks that other modules can subscribe to (e.g. a search module may want to update it's index after every save in your news module)</li>
	<li><a href="<?=user_guide_url('general/forms')?>">Custom Fields</a>: Create custom form field types for you forms</li>
	<li><a href="<?=user_guide_url('modules/simple#overwrites')?>">Module Overwrites</a>: Overwrite existing module parameters</li>
	<li><a href="<?=user_guide_url('modules/advanced')?>">Advanced Modules</a>: Create your own specific application folder with self-contained libraries, helpers, controllers, views etc.</li>
	<li><a href="<?=user_guide_url('modules/simple')?>">Simple Modules</a>: A model that can allow users to manage data in the CMS</li>
	<li><a href="<?=user_guide_url('general/dashboards')?>">Dashboards</a>: A view to display on the Dashboard (e.g. Google Analytics graph)</li>
	<li><a href="<?=user_guide_url('modules/tools')?>">Module Tools</a>: Tools to be used while inline editing a page (e.g. run a keyword page analysis on a specific page, validate the HTML, or validate the links on the page)</li>
</ul>
