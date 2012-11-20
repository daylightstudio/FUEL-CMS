<h1>What's New with FUEL CMS 1.0</h1>
<p>A lot...</p>

<h2>WARNINGS!!!</h2>
<ul>
	<li>Page variable data is now saved in a JSON format instead of a serialized PHP string</li>
	<li>Several tables have been updated</li>
</ul>

<h2>General</h2>
<ul>
	<li>Extracted much of the controller functionality into library classes so you can now have access to methods like $this->fuel->backup->do_backup(); or $this->fuel->pages->create();</li>
	<li>Moved all non-extended classes and helpers into the modules/fuel folder to cleanup the application directory some</li>
	<li>Separated out all modules except the "fuel" module into their own <a href="https://github.com/daylightstudio" target="_blank">GitHub repos</a></li>
</ul>


<h2>Libraries</h2>

<h3>New</h3>
<ul>
	<li><a href="<?=user_guide_url('libraries/curl')?>">Curl</a></li>
	<li><a href="<?=user_guide_url('libraries/inspection')?>">Inspection</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_assets')?>">Fuel_assets</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_categories')?>">Fuel_categories</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_language')?>">Fuel_language</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_notification')?>">Fuel_notification</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_redirects')?>">Fuel_redirects</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_settings')?>">Fuel_settings</a></li>
	<li><a href="<?=user_guide_url('libraries/fuel_tags')?>">Fuel_tags</a></li>
</ul>

<h3>Models</h3>

<h3>MY_Model</h3>
<ul>
	<li>Added belongs_to and has_many properties for relationships</li>
	<li>Added formatters to manipulate field values (e.g. date_formatted)</li>
	<li>Added ability to create custom field objects (e.g. $my_record->image->path())</li>
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
	<li>Added generic Tags and Categories module (hidden by default in fuel/application/config/MY_fuel_modules.php)</li>
</ul>

<h3>Blog</h3>
<ul>
	<li>Added main, list and thumbnail image fields to blog posts</li>
	<li>Added ability to associate blocks to a blog</li>
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
	<li>Organized and added more examples</li>
	<li>Fixed typos (added some too I'm sure)</li>
	<li>Added toggling of methods/functions to display more information</li>
</ul>

<h2>Helpers</h2>

<h3>New</h3>
<ul>
	<li>Scraper helper</li>
	<li>Simplepie helper</li>
	<li>Session helper</li>
</ul>

<h3>MY_url_helper</h3>
<ul>
	<li>Combined https_site_url with site_url (added an additional parameter)</li>
	<li>Added link_target function</li>
	<li>Added last_url function</li>
	<li>Added redirect_404 function</li>
</ul>


<h3>fuel_helper</h3>
<ul>
	<li>Added FUEL and fuel_instance functions</li>
	<li>Added fuel_form function</li>
	<li>Added fuel_page function</li>
	<li>Added ability to only need to pass in a record object into the fuel_edit function</li>
</ul>

<h2>Misc.</h2>
<ul>
	<li>Added jquery UI to the CMS</li>
	<li>Added ability to pass more then one field to edit inline</li>
	<li>Added ability to export data from module by adding an "export_data" method to your model</li>
</ul>

