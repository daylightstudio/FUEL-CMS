<h1>What's New in FUEL CMS 1.0</h1>
<p>A lot... we've summarized the highlights below:</p>

<h2>WARNING!!!</h2>
<ul>
	<li>Page variable data is now saved in a JSON format instead of a serialized PHP string</li>
	<li>Several tables have been updated with the addition of several tables (e.g. fuel_categories, fuel_tags, fuel_settings, fuel_relationships) and minor changes to existing ones. There is a <span class="file">fuel/install/upgrades/fuel_1.0_schema_changes.sql</span> file you can run to upgrade the schema.</li>
	<li>User to permission and blog post to category relationships are now stored in the <a href="<?=user_guide_url('general/models#relationships')?>">fuel_relationships</a> table.</li>
</ul>

<h2>General</h2>
<ul>
	<li>Extracted much of the controller functionality into library classes so you can now have access to methods like <dfn>$this->fuel->backup->do_backup()</dfn> or <dfn>$this->fuel->pages->create()</dfn>. 
		Read more on the <a href="<?=user_guide_url('general/fuel-object-structure')?>">FUEL object structure</a></li>
	<li>Moved all non-extended classes and helpers into the modules/fuel folder to cleanup the application directory</li>
	<p>Improved the security hashing used for storing passwords in the database</p>
	<li>Separated out all other modules into their own <a href="https://github.com/daylightstudio" target="_blank">GitHub repos</a> 
		(e.g. <a href="https://github.com/daylightstudio/FUEL-CMS-Backup-Module" target="_blank">backup</a>, 
		<a href="https://github.com/daylightstudio/FUEL-CMS-Blog-Module" target="_blank">blog</a>, 
		<a href="https://github.com/daylightstudio/FUEL-CMS-Cronjobs-Module" target="_blank">cronjob</a>, 
		<a href="https://github.com/daylightstudio/FUEL-CMS-Page-Analysis-Module" target="_blank">page_analysis</a>,  
		<a href="https://github.com/daylightstudio/FUEL-CMS-Google-Keywords-Module" target="_blank">google_keywords</a>,  
		<a href="https://github.com/daylightstudio/FUEL-CMS-Tester-Module" target="_blank">tester</a>, 
		<a href="https://github.com/daylightstudio/FUEL-CMS-User-Guide-Module" target="_blank">user_guide</a>, 
		<a href="https://github.com/daylightstudio/FUEL-CMS-Validate-Module" target="_blank">validate</a>) </li>
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

<h3>Form_builder</h3>
<ul>
	<li>Added ability to create custom fields that can be associated with their own layout, javascript and CSS</li>
	<li>Added a lot of <a href="<?=user_guide_url('general/forms')?>">new custom field types</a> including the powerful <a href="<?=user_guide_url('general/forms#template')?>">template</a> field type</li>
	<li>Added custom date formatting support</li>
</ul>

<h2>Helpers</h2>

<h3>New</h3>
<ul>
	<li><a href="<?=user_guide_url('helpers/scraper_helper')?>">Scraper helper</a></li>
	<li><a href="<?=user_guide_url('helpers/simplepie_helper')?>">Simplepie helper</a></li>
	<li><a href="<?=user_guide_url('helpers/session_helper')?>">Session helper</a></li>
</ul>

<h3>MY_url_helper</h3>
<ul>
	<li>Combined https_site_url with <a href="<?=user_guide_url('helpers/my_url_helper#func_site_url')?>">site_url</a> (added an additional parameter)</li>
	<li>Added <a href="<?=user_guide_url('helpers/my_url_helper#func_link_target')?>">link_target</a> function</li>
	<li>Added <a href="<?=user_guide_url('helpers/my_url_helper#func_last_url')?>">last_url</a> function</li>
	<li>Added <a href="<?=user_guide_url('helpers/my_url_helper#func_redirect_404')?>">redirect_404</a> function</li>
</ul>

<h3>fuel_helper</h3>
<ul>
	<li>Added <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel')?>">FUEL</a> and <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_instance')?>">fuel_instance</a> functions</li>
	<li>Added <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_form')?>">fuel_form</a> function</li>
	<li>Added <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_page')?>">fuel_page</a> function</li>
	<li>Added ability to only need to pass in a record object into the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_edit')?>">fuel_edit</a> function</li>
</ul>

<h3>utility_helper</h3>
<ul>
	<li>Added <a href="<?=user_guide_url('helpers/utility_helper#func_log_error')?>">log_error</a> function</li>
	<li>Added <a href="<?=user_guide_url('helpers/utility_helper#func_is_environment')?>">is_environment</a> function</li>
</ul>

<h2>Models</h2>

<h3>MY_Model</h3>
<ul>
	<li>Added belongs_to and has_many properties for relationships</li>
	<li>Added formatters to manipulate field values (e.g. date_formatted)</li>
	<li>Added ability to create custom field objects (e.g. <dfn>$my_record->image->path()</dfn>)</li>
	<li>Added boolean_fields property to MY_Model which allows for toggling of boolean values in the module's list view</li>
	<li>Added the is_{property} method for all boolean type properties (e.g. <dfn>$my_rec->is_published()</dfn>)</li>
	<li>Added the has_{property} method for all properties (e.g. <dfn>$my_rec->has_description()</dfn>)</li>
	<li>Added where_in if the value of the where parameter is a nested array to the find_all method</li>
</ul>

<h3>Base_module_model</h3>
<ul>
	<li>Added ability to set related items panels information (e.g. related permssions in the permissions module)</li>
	<li>Simplified the ability to create tree structures for models that have has_many, belongs_to or foreign_key relationships</li>
	<li>Added ability to export data from module by adding an "export_data" method to your module's model</li>
	<li>Added ability to replace record values with existing ones. This is useful in situations where you have say a published page at the location
		<dfn>about</dfn> and an unpublished version at <dfn>about_1</dfn> that you want to instantly replace.</li>
	<li>Improved list item filtering capabilities</li>
</ul>

<h2>Advanced Modules</h2>

<h3>General</h3>
<ul>
	<li>Extracted all relevant controller logic for modules into their own library to be used on the FUEL object (e.g. <dfn>$this->fuel->backup->do_backup()</dfn>)</li>
	<li>Added ability to add a module as GIT submodule and install via command line</li>
	<li>Added ability to generate advanced and simple modules via command line</li>
	<li>Broke out Google Keywords and Page Analysis into their own modules (was "seo" module)</li>
</ul>

<h3>Fuel</h3>
<ul>
	<li>Added ability to login as another user to test their permissions without knowing their password if you are an admin</li>
	<li>Added generic Tags and Categories module (hidden by default in <span class="file">fuel/application/config/MY_fuel_modules.php</span>)</li>
</ul>

<h3>Blog</h3>
<ul>
	<li>Added main, list and thumbnail image fields to blog posts</li>
	<li>Added ability to associate blocks to a blog</li>
	<li>Added hooks to the blog to manipulate the list view</li>
</ul>

<h3>Tester</h3>
<ul>
	<li>Updated Tester module so that it can run via command line</li>
</ul>

<h3>User Guide</h3>
<ul>
	<li>Added ability to auto generate documentation on Library and Helper files</li>
	<li>Added ability to associate examples to generated documentation</li>
	<li>Added keyboard shortcut of Shift+Space to toggle the Table of Contents</li>
	<li>Organized and added more examples</li>
	<li>Fixed typos (added some too I'm sure)</li>
	<li>Added toggling of methods/functions to display more information</li>
</ul>

<h3>Validate</h3>
<ul>
	<li>Updated the Validate module so that you can run validation in your own controllers $this->fuel->validate->html('my_page');</li>
</ul>




<h2>Inline Editing</h2>
<ul>
	<li>Added inline editing tools in the toolbar that other modules can take advantage of (e.g. the page_analysis module and validation module)</li>
	<li>Ability to add more then one field to display to edit if separated by a ":" (e.g. <dfn>&lt;?=fuel_edit($item->id.'|title:slug', 'Edit:'.$item->title, 'news')?&gt;</dfn></li>
	<li>Ability to pass additional parameters (e.g. foriegn key values) when creating a new record (e.g. <dfn>&lt;?=fuel_edit('create?title=TITLE%20GOES%20SHERE', 'Add', 'news')?&gt;</dfn></li>
	<li>Allow for displaying unpublished content on the front end if you are logged in. Will display a slightly different icon if unpublished or not active (display_unpublished_if_logged_in property on model is set to TRUE)</li>
</ul>


<h2>Language</h2>
<ul>
	<li>Added ability to set different languages for pages</li>
	<li>Added automatic filtering dropdown for simple modules with a "language" column</li>
	<li>Added Fuel_language class</li>
</ul>



<h2>Misc.</h2>
<ul>
	<li>Added jquery UI to the CMS</li>
	<li>Added ability to pass more then one field to edit inline</li>
	<li>Improved importing of views into the CMS to capture set variables in the view</li>
	<li>Added migration support with <span class="file">fuel/migrate/latest</span></li>
</ul>

