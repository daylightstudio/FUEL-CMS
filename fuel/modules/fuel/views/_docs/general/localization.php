<h1>Language/Localization</h1>
<p>FUEL CMS 1.0 now provides basic multi-language support through the CMS for pages and modules. You can configure the number of 
supported languages in the <a href="<?=user_guide_url('installation/configuration')?>">FUEL config</a>
by adding to the <dfn>$config['languages']</dfn> parameter. The <a href="<?=user_guide_url('libraries/fuel_language')?>">Fuel_language</a> class
can then be used to help set and retrieve language information.</p>


<h2>Creating a Multi-language Website</h2>
<p>There are a few different ways to add multi-language support to your website:</p>

<h3>CodIgniter's Language Class</h3>
<p>CodeIgniter comes with a <a href="http://ellislab.com/codeigniter/user-guide/libraries/language.html" target="_blank">Language class</a> which can is fully available for you to leverage in your site.</p>

<h3>CMS Pages</h3>
<p>When multiple languages are configured in the <a href="<?=user_guide_url('installation/configuration')?>">FUEL config</a>, you will see an additional drop down when editing or creating a page in the CMS.
Each page location can have multiple languages assigned to it. Then to display the different language versions of the page, you can pass the <dfn>lang</dfn> query string parameter like so:</p>
<pre class="brush:php">
http://localhost/about/history?lang=es
</pre>

<p>Additionally you can set the language of your site for the current user as follows:</p>
<pre class="brush:php">
$this->fuel->language->set_selected('es');
</pre>

<p>That user is then cookied to use that language going forward.</p>

<h3>View Pages</h3>
<p>Additionally, if your pages are using views  and there is a language value set by the user using <a href="<?=user_guide_url('libraries/fuel_language')?>">Fuel_language</a>, 
it will first look for a view file in the <span class="file">views/language/{language}/</span> folder and if it doesn't find it, it will default to just the views folder.</p>

<h3>CMS Modules</h3>
<p>Blocks, Navigation and your own custom simple modules behave a little differently then the Page module. A new record must be created for each language version.
These modules have a field name of <dfn>language</dfn> that will appear if more then one language is configured. For Blocks and Navigation, 
the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_block')?>">fuel_blocks</a> and <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_nav')?>">fuel_nav</a> functions 
have a "language" parameter you can specify which will pull blocks or menu items specific to that language.

<p class="important">This field name can be configured by specifying the <a href="<?=user_guide_url('modules/simple')?>">simple module's</a> <dfn>language_col</dfn> parameter.</p>


<h2>Changing the Language Displayed in the CMS</h2>
<p>The default installation of FUEL CMS currently only comes with the English langauge. 
However, localization efforts are underway on the <a href="https://github.com/daylightstudio/FUEL-CMS-Languages" target="_blank">FUEL CMS Languages GitHub repo</a>.
If you are interested in helping out with language support, just let us know or send us a pull request (we love those).</p>


<p>To add a language to FUEL, you must <a href="https://github.com/daylightstudio/FUEL-CMS-Languages" target="_blank">download the latest 1.0 language folder (if available).</a> and
place it's contents in the <span class="file">fuel/modules/fuel/language</span> folder. Then, you can set the <dfn>$config['language']</dfn> in the <span class="file">fuel/application/config.php</span>
file to match the name of the language folder you downloaded. Alternatively, you may click on your user name in the upper right corner of the CMS, and then change the language associated
with your urser login.</p>


<p>The following are some key areas in which you can utilize FUEL's localization functionality.</p>

<h2>The lang() Function</h2>
<p>The <a href="http://www.getfuelcms.com/user_guide/helpers/my_language_helper" target="_blank">lang()</a> overwrites the CodeIgniter lang() function to provide the added benfits of passing arguments to your language files (uses sprintf() function).</p>

<h2>The json_lang() Function</h2>
<p>The <a href="http://www.getfuelcms.com/user_guide/helpers/my_language_helper" target="_blank">json_lang()</a> function allows for language values to be passed to the javascript. This function creates a Javascript JSON object based on an array of language key values.</p>

<h2>Form_builder Class</h2>
<p>The <a href="http://www.getfuelcms.com/user_guide/libraries/form_builder" target="_blank">Form_builder class</a> is used throughout FUEL CMS to create the forms used to manage module data. To allow for the localized labels in the module forms, the <var>lang_prefix</var> property can be used and will look for labels in language files using the format <var>form_label_{literal}{field_name}{/literal}</var>, if not label value is supplied.</p>

<h2>Data_table Class</h2>
<p>Similar to the Form_builder class, the <a href="http://www.getfuelcms.com/user_guide/libraries/data_table" target="_blank">Data_table class</a> also has a <var>lang_prefix</var> property. This prefix is used for localizing the table column headers. The prefix is set in FUEL to be the same as the Form_builder's which is <var>form_label_</var>.</p>


<h2>The js_localized Module Property</h2>
<p>The <a href="http://www.getfuelcms.com/user_guide/modules/simple" target="_blank">js_localized</a> property can be added to your modules if you have have javascript that needs to use some localized text. You can provide it an array of language key values and it will be added to the list of language keys that get translated into a JSON object for your javascript files to consume. If you are using a <a href="http://www.getfuelcms.com/user_guide/javascript/jqx" target="_blank">jqx</a> type controller that extends the BaseFuelController.js, there will be a <var>localized property</var> and a lang() method on the controller that provides access to the JSON language object.</p>


