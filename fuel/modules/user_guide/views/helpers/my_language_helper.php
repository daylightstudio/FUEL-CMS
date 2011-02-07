<h1>MY Language Helper</h1>

<p>Contains functions to be used with language files. Extends CI's language helper and is <strong>autoloaded</strong>.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('language');
</pre>

<p>The following functions are available:</p>

<h2>lang(<var>str</var>, <var>[vars]</var>)</h2>
<p>Get translated local strings with arguments.</p>

<pre class="brush: php">
// in lang file
$lang['lang_key'] = "This is a lang file entry for %1s.";
...
echo lang('lang_key', 'my_param'); //This is lang file entry for my_param.
</pre>

<h2>json_lang(<var>array_or_string</var>, <var>[return_json]</var>)</h2>
<p>Creates an array or JSON object that can be used by your javascript files that need localized text. If the first
parameter is an array, then it will include any language keys that are in the array.
If the first parameter is a string, it will search for a language file to include. Language files within a module 
should include the slash (e.g. fuel/fuel_js). </p>

<pre class="brush: php">
// in lang file
$lang['lang_key'] = "This is a lang file entry for %1s.";
...
echo json_lang($lang, TRUE); //{lang_key:'This is a lang file entry for %1s.'}


// OR in lang file of 'fuel/fuel_js'
$lang['lang_key'] = "This is a lang file entry for %1s.";
...
echo json_lang('fuel/fuel_js', FALSE); //array('lang_key' => 'This is a lang file entry for %1s.'...)

</pre>
