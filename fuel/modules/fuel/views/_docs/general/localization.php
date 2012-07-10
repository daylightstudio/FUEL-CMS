<h1>Localization</h1>
<p>FUEL CMS 1.0 now provides basic multi-language support through the CMS for pages and modules. You can configure the number of 
supported languages to display for your pages in <a href="<?=user_guide_url('installation/configuration')?>">FUEL config</a>.
Additionally, if you have a simple module has a field named "language", This field name can be configured by specifying
the  <a href="<?=user_guide_url('modules/simple')?>">simple module's</a> <dfn>language_col</dfn> parameter.
</p>

<p>The default installation of FUEL CMS currently only comes with the English langauge. 
However, localization efforts are underway on the <a href="https://github.com/daylightstudio/FUEL-CMS-Languages" target="_blank">FUEL CMS Languages GitHub repo</a>.
</p>

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


