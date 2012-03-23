<h1>What's New with FUEL CMS 1.0</h1>
<ul>
	<li>Extracted much of the controller functionality into library classes so you can now have access to methods like $this->fuel->backup->do_backup(); or $this->fuel->pages->create();</li>
	<li>Added boolean_fields property to MY_Model which allows for toggling of boolean values in the module's list view</li>
	<li>Added inline editing tools in the toolbar that other modules can take advantage of (e.g. the page_analysis module and validation module)</li>
	<li>Moved all non-extended classes and helpers into the modules/fuel folder to cleanup the application directory some</li>
	<li>Separated out all modules except the fuel module into their own development repos</li>
</ul>

<h2>Libraries</h2>
<ul>
	<li>Curl</li>
	<li>Inspection</li>
</ul>

<h2>Other modules</h2>
<ul>
	<li>Updated backup module</li>
	<li>Updated tester module so that it can run via command line</li>
	<li>Updated the validate module so that you can run validation in your own controllers $this->fuel->validate->html('my_page');</li>
	<li>Broke out Google Keywords and Page Analysis into their own modules (was "seo" module);</li>
</ul>


User Guide:
<ul>
	<li>Added ability to auto generate documentation on Library and Helper files</li>
	<li>Added ability to associate examples to generated documentation</li>
	<li>Added keyboard shortcut of Control+Shift+T to toggle the Table of Contents</li>
	<li>Added saving display state of the TOC to a cookie</li>
	<li>Organized and added more examples</li>
	<li>Fixed typos (added some too I'm sure)</li>
	<li>Added toggling of methods/functions to display more information</li>
</ul>

Blog
<ul>
	<li>Added image sets to blog</li>
	<li>Added hooks to the blog to manipulate the list view</li>
</ul>

Added Classes
<ul>
	<li>Inspection class</li>
	<li>Curl class - which includes support for multiple CURL sessions</li>
</ul>

Added Helpers
<ul>
	<li>scraper helper</li>
	<li>simplepie helper</li>
	<li>session helper</li>
</ul>

Form_builder

Inline Editing


fuel_helper
<ul>
	<li>added FUEL and fuel_instance functions</li>
	<li>added fuel_form function</li>
	<li>added fuel_page function</li>
	<li>added ability to only need to pass in a record object into the fuel_edit function</li>
</ul>

MISC
<ul>
	<li>Added jquery UI to the backend</li>
	<li>Added ability to pass more then one field to edit inline</li>
</ul>

WARNINGS
<ul>
	<li>page variable data is now saved in a JSON format which is not backwards compatible (reason was to prevent issues with serialization and some multi-byte characters)</li>
	<li>several tables have been updated</li>
	<li>user password now use a stronger encryption and requires a password reset</li>
</ul>
