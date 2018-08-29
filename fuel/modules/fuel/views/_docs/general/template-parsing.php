<h1>Template Parsing</h1>

<h2>Overview</h2>
<p>By default, FUEL disables PHP code from being saved in your module data 
(you must change the <a href="<?=user_guide_url('modules/simple')?>">sanitize_input</a> setting for the module).
FUEL has three options to choose from for the parsing engine of <a href="https://ellislab.com/codeigniter/user-guide/libraries/parser.html" target="_blank">ci</a>, 
<a href="http://dwoo.org" target="_blank">Dwoo</a> and <a href="http://twig.sensiolabs.org" target="_blank">Twig (new!)</a>. 
The default parsing engine is currently Dwoo for legacy reasons but has been deprecated in favor of Twig. New to FUEL CMS 1.3 are some additional FUEL template configurations
that can be overwritten in your <span class="file">fuel/application/config/MY_fuel.php</span> file:
</p>
<pre class="brush:php">
// The directory to put the parsed compiled files
$config['parser_compile_dir'] = APPPATH.'cache/dwoo/compiled/';

// The delimiters used by the parsing engine
$config['parser_delimiters'] = array(
				'tag_comment'   => array('{#', '#}'), // Twig only
				'tag_block'     => array('{%', '%}'), // Twig only
				'tag_variable'  => array('{', '}'), // Used by Twig, Dwoo and CI. Default for twig is '{{', '}}'
				'interpolation' => array('#{', '}'), // Twig only
			);

// Functions allowed by the parsing engine
$config['parser_allowed_functions'] = array(
	'strip_tags', 'date', 
	'detect_lang','lang',
	'js', 'css', 'swf', 'img_path', 'css_path', 'js_path', 'swf_path', 'pdf_path', 'media_path', 'cache_path', 'captcha_path', 'assets_path', // assets specific
	'fuel_block', 'fuel_model', 'fuel_nav', 'fuel_edit', 'fuel_set_var', 'fuel_var', 'fuel_var_append', 'fuel_form', 'fuel_page', // FUEL specific
	'quote', 'safe_mailto', // HTML/URL specific
	'session_flashdata', 'session_userdata', // Session specific
	'prep_url', 'site_url', 'show_404', 'redirect', 'uri_segment', 'auto_typography', 'current_url' // CI specific
);

// Object references passed to the parsing engine
$config['parser_refs'] = array('config', 'load', 'session', 'uri', 'input', 'user_agent');
</pre>

<p class="important"><strong>Note that the default parsing delimiters are set to "{" and "}" which is different then the default Twig parsing parameters. This is done for legacy reasons.</strong></p>

<h3>Fuel_parser Class</h3>
<p>New to FUEL CMS 1.3 is the <a href="<?=user_guide_url('libraries/fuel_parser')?>">Fuel_parser</a> class which provides a unified way to dealing with the different parsing engines now available in FUEL.</p>


<h3>String Helper Functions</h3>
<p>FUEL comes with the following <a href="<?=user_guide_url('helpers/my_string_helper')?>">string helper functions</a> to help with parsing and converting from the Dwoo templating syntax:</p>
<ul>
	<li>php_to_template_syntax - Convert PHP syntax to the specified templating syntax (as best it can). Must use the PHP alternative syntax for if and foreach loops to be translated correctly.</li>
	<li>parse_template_syntax - Parses a strings specified templating syntax.</li>
</ul>

<p class="important"><strong>The below documentation is specific to the Dwoo templating engine. 
	See the <a href="http://twig.sensiolabs.org" target="_blank">Twig documentation</a> for Twig specific documentation.</strong>
</p>

<h3>Non-Namespaced Functions</h3>
<p>The following are non-namespaced functions that can be used in your application and will be translated by the templating system (below are Dwoo examples).</p>
<ul>
	<li>{site_url('/my/path/')}</li>
	<li>{assets_path('images/my_asset.jpg')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_assets_path')?>">assets_path()</a> function</li>
	<li>{img_path('my_img.jpg')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_img_path')?>">img_path()</a> function.</li>
	<li>{js_path('my_js.js')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_js_path')?>">js_path()</a> function. The <dfn>.js</dfn> extension is optional.</li>
	<li>{swf_path('my_swf.swf')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_swf_path')?>">swf_path()</a> function. The <dfn>.swf</dfn> extension is optional.</li>
	<li>{media_path('my_movie.mov')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_media_path')?>">media_path()</a> function.</li>
	<li>{pdf_path('my_pdf.pdf')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_pdf_path')?>">pdf_path()</a> function. The <dfn>.pdf</dfn> extension is optional.</li>
	<li>{safe_mailto('my@email.com', 'text')} - Maps to the  <a href="https://www.codeigniter.com/user_guide/helpers/url_helper.html#safe_mailto" target="_blank">safe_mailto()</a> function.</li>
	<li>{redirect('my_redirect_page')} - Maps to the <a href="https://www.codeigniter.com/user_guide/helpers/url_helper.html#redirect" target="_blank">redirect()</a> function.</li>
	<li>{show_404} - Maps to the <a href="https://www.codeigniter.com/user_guide/general/errors.html#show_404" target="_blank">show_404()</a> function.</li>
</ul>

<h3>Namespaced Functions</h3>
<p>The following are namespaced functions that can be used in your application and will be translated by the templating system (below are Dwoo examples).</p>

<ul>
	<li>{uri_segment(1, true/false)} - Maps to the <a href="<?=user_guide_url('helpers/my_url_helper#func_uri_segment')?>">uri_segment(n)</a> function.</li>
	<li>{fuel_var} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_var')?>">fuel_var()</a> function.</li>
	<li>{fuel_model('module_name', array(key="val"...)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_model')?>">fuel_model()</a> function. The first parameter is usually the name of your model and does not need to include the "_model" suffix (usually the same as the module name).</li>
	<li>{fuel_block(array(key="val"...))} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_block')?>">fuel_block()</a> function.</li>
	<li>{fuel_nav(array(key="val"...))} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_nav')?>">fuel_nav()</a> function.</li>
	<li>{fuel_edit(id, label, module, xOffset, yOffset)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_edit')?>">fuel_edit()</a> function.</li>
</ul>
<p class="important">Note that several of the functions require an associative array parameter with the <dfn>key="val"</dfn> syntax.</p>

<h3>Blocks</h3>
<p>Tag pairs allow you to loop through arrays of data. The syntax requires an opening <dfn>{my_var}</dfn> and closing <dfn>{/my_var}</dfn> tag. 
For example:</p>

<pre class="brush:php">
$my_data = array();
$my_data[] = array('name' => 'Darth Vader', 'weapon' => 'light saber');
$my_data[] = array('name' => 'Han Solo', 'weapon' => 'blaster');
...
{loop $my_data}
	{$name} - {$weapon}
{loop}
</pre>

<p>You can also iterate over objects. For example, you may have a user model with some custom methods on it:</p>
<pre class="brush:php">
... 
{$mydata = fuel_model('users', find="all")}

{foreach $my_data user}
	{$user->name} - {$user->weapon}
{/foreach}
</pre>

<p class="important">The <dfn>user</dfn> part can actually be any value. You cannot insert outside variables inside a tag pair block.</p>

<h3>Conditional Statements</h3>
<p>FUEL also allows for php like conditional statements to be inserted into the view using <dfn>{if ... }</dfn>, <dfn>{elseif ...}</dfn> and <dfn>{/if}</dfn>.
For example (Dwoo example):
</p>

<pre class="brush:php">
{$name='Darth Vader'}
{if $name == 'Darth Vader'}
I am your father.
{/if}
</pre>


<h3>Creating your own tags</h3>
<p><a href="http://wiki.dwoo.org/index.php/WritingPlugins" target="_blank">Dwoo provides several ways for it to extend it's templating syntax through plugins</a>. 
The Dwoo folder to add your plugins is located at <dfn>application/libraries/dwoo/plugins</dfn>.
</p>


<h2>Examples</h2>
<p>Below are examples of FUEL's supported parsing syntax. For even more information, visit the <a href="http://wiki.dwoo.org/index.php/Main_Page" target="_blank">Dwoo Wiki</a></p>

<h3 class="noline">Scalar Variable Merge</h3>

<pre class="brush:php">
$var['name'] = 'Luke';
$str = '{$name}, I am your father.';

$parsed_str = $this->parser->parse_string($str, $var);
</pre>


<h3 class="noline">Array Variable Merge</h3>
<pre class="brush:php">
$var['darths_kiddos'] = array();
$var['darths_kiddos'][] = array('name' => 'Luke Skywalker', 'relationship' => 'son');
$var['darths_kiddos'][] = array('name' => 'Princess Leia', 'relationship' => 'daughter');

$str = '
{loop $darths_kiddos}
	{$name} is Vader\'s {$relationship}. 
{/loop}';

$parsed_str = $this->parser->parse_string($str, $var);
</pre>


<h3 class="noline">Object Merge</h3>
<pre class="brush:php">
$this->load->model('fuel_users_model');
$data['users'] = $this->fuel_users_model->find_by_key(1);
$template = '{$user->full_name}';
$test = $this->parser->parse_string($template, $data);
echo $test; //'Darth Vader';
</pre>


<h3 class="noline">Array of Objects Merge</h3>
<pre class="brush:php">
$this->load->model('fuel_users_model');
$data['users'] = $this->fuel_users_model->find_all();
$str = '
{loop $users}
<h3>{$user->first_name} {$user->last_name}</h3>
{$user->get_bio_formatted()}
{/loop}';

$parsed_str = $this->parser->parse_string($str, $data);
</pre>


<h3 class="noline">Conditionals</h3>
<pre class="brush:php">
{if 1 + 1 == 2}
One plus one equals 2!
{/if}

{$my_var="test"}
{if $my_var == "test"}
my_var equals the word "test"!
{/if}
</pre>