<h1>What's New with FUEL CMS 1.0</h1>
<p><a href="#">A lot...</a>.</p>

<h2>WARNINGS!!!</h2>
<ul>
	<li>Page variable data is now saved in a JSON format</li>
	<li>Several tables have been updated</li>
	<li>User passwords now use a stronger encryption and requires a password reset</li>
</ul>

<h2>General</h2>
<ul>
	<li>Extracted much of the controller functionality into library classes so you can now have access to methods like $this->fuel->backup->do_backup(); or $this->fuel->pages->create();</li>
	<li>Moved all non-extended classes and helpers into the modules/fuel folder to cleanup the application directory some</li>
	<li>Separated out all modules except the fuel module into their own development repos</li>
</ul>


<h2>Libraries</h2>

<h3>New</h3>
<ul>
	<li><a href="<?=user_guide_url('libraries/curl')?>">Curl</a></li>
	<li><a href="<?=user_guide_url('libraries/inspection')?>">Inspection</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_notification')?>">Fuel_notification</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_settings')?>">Fuel_settings</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_language')?>">Fuel_language</a></li>
</ul>

<h3>Models</h3>

<h3>MY_Model</h3>
<ul>
	<li>Added belongs_to and has_many properties</li>
	<li>Added boolean_fields property to MY_Model which allows for toggling of boolean values in the module's list view</li>
	<li>Added the is_{property} method for all boolean type properties (e.g. $my_rec->is_published())</li>
	<li>Added the has_{property} method for all properties (e.g. $my_rec->has_description())</li>
	<li>Added where_in if the value of the where parameter is a nested array to the find_all method</li>
</ul>

<h2>Modules</h2>

<h3>General</h3>
<ul>
	<li>Extracted all relevant controller logic for modules into their own library to be used on the FUEL object (e.g. $this->fuel->backup->do_backup())</li>
	<li>Added ability to add a module as GIT submodule and install via command line</li>
	<li>Added ability to generate advanced and simple modules via command line</li>
	<li>Updated Tester module so that it can run via command line</li>
	<li>Updated the Validate module so that you can run validation in your own controllers $this->fuel->validate->html('my_page');</li>
	<li>Broke out Google Keywords and Page Analysis into their own modules (was "seo" module);</li>
</ul>

<h3>Blog</h3>
<ul>
	<li>Added image sets to blog</li>
	<li>Added hooks to the blog to manipulate the list view</li>
</ul>



<h2>Inline Editing</h2>
<ul>
	<li>Added inline editing tools in the toolbar that other modules can take advantage of (e.g. the page_analysis module and validation module)</li>
	<li>Ability to add more then one field to display to edit separating by a ":"</li>
	<li>Ability to pass additional parameters (e.g. foriegn key values) when creating a new record</li>
	<li>Allow for displaying unpublished content on the front end if you are logged in. Will display a slightly different icon if unpublished or not active (display_unpublished_if_logged_in propert on model)</li>
</ul>


<h2>Language</h2>
<ul>
	<li>Added ability to set different languages for pages</li>
	<li>Added Fuel_language class</li>
</ul>


<h2>User Guide</h2>
<ul>
	<li>Added ability to auto generate documentation on Library and Helper files</li>
	<li>Added ability to associate examples to generated documentation</li>
	<li>Added keyboard shortcut of Shift+Space to toggle the Table of Contents</li>
	<li>Added saving display state of the TOC to a cookie</li>
	<li>Organized and added more examples</li>
	<li>Fixed typos (added some too I'm sure)</li>
	<li>Added toggling of methods/functions to display more information</li>
</ul>

<h2>Helpers</h2>

<h3>New</h3>
<ul>
	<li>scraper helper</li>
	<li>simplepie helper</li>
	<li>session helper</li>
	<li>database helper</li>
</ul>

<h3>MY_url_helper</h3>
<ul>
	<li>added link_target</li>
	<li>added last_url</li>
	<li>added redirect_404</li>
</ul>


<h3>fuel_helper</h3>
<ul>
	<li>added FUEL and fuel_instance functions</li>
	<li>added fuel_form function</li>
	<li>added fuel_page function</li>
	<li>added ability to only need to pass in a record object into the fuel_edit function</li>
</ul>

<h2>Misc.</h2>
<ul>
	<li>Added jquery UI to the backend</li>
	<li>Added ability to pass more then one field to edit inline</li>
	<li>Added ability to export data from module by adding an "export_data" method to your model</li>
	<!--<li>Added test_connection method on Mysql driver</li>-->
</ul>

