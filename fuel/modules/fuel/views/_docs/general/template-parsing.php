<h1>Temlate Parsing</h1>

<h2>Overview</h2>
<p>By default, FUEL disables PHP code from being saved in your module data 
(you must change the <a href="<?=user_guide_url('modules/simple')?>">sanitize_input</a> setting for the module).
FUEL has implemented the <a href="http://dwoo.org/" target="_blank">Dwoo</a> PHP 5 based templating system.
</p>

<h3>MY_Parser</h3>
<p>FUEL overwrites the default CodeIgniter Parser library with the <a href="<?=user_guide_url('libraries/my_parser')?>">MY_Parser</a> 
library to implement the expanded Dwoo templating syntax.</p>


<h3>String Helper Functions</h3>
<p>FUEL comes with the following <a href="<?=user_guide_url('helpers/my_string_helper')?>">string helper functions</a> to help with parsing and converting from the Dwoo templating syntax:</p>
<ul>
	<li>php_to_template_syntax - Convert PHP syntax to <a href="http://dwoo.org" target="_blank">Dwoo templating syntax</a>. Must use the PHP alternative syntax for if and foreach loops to be translated correctly.</li>
	<li>parse_template_syntax - Parses a strings <a href="http://dwoo.org" target="_blank">Dwoo templating syntax</a>.</li>
</ul>

<h3>Non-Namespaced Functions</h3>
<p>The following are non-namespaced functions that can be used in your application and will be translated by the templating system.</p>
<ul>
	<li>{site_url('/my/path/')}</li>
	<li>{assets_path('images/my_asset.jpg')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_assets_path')?>">assets_path()</a> function</li>
	<li>{img_path('my_img.jpg')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_img_path')?>">img_path()</a> function.</li>
	<li>{js_path('my_js.js')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_js_path')?>">js_path()</a> function. The <dfn>.js</dfn> extension is optional.</li>
	<li>{swf_path('my_swf.swf')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_swf_path')?>">swf_path()</a> function. The <dfn>.swf</dfn> extension is optional.</li>
	<li>{media_path('my_movie.mov')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_media_path')?>">media_path()</a> function.</li>
	<li>{pdf_path('my_pdf.pdf')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper#func_pdf_path')?>">pdf_path()</a> function. The <dfn>.pdf</dfn> extension is optional.</li>
	<li>{safe_mailto('my@email.com', 'text')} - Maps to the  <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">safe_mailto()</a> function.</li>
	<li>{redirect('my_redirect_page')} - Maps to the <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">redirect()</a> function.</li>
	<li>{show_404} - Maps to the <a href="http://codeigniter.com/user_guide/general/errors.html" target="_blank">show_404()</a> function.</li>
</ul>

<h3>Namespaced Functions</h3>
<p>The following are namespaced functions that can be used in your application and will be translated by the templating system.</p>

<ul>
	<li>{uri_segment(1, true/false)} - Maps to the <a href="<?=user_guide_url('helpers/my_url_helper#func_uri_segment')?>">uri_segment(n)</a> function.</li>
	<li>{fuel_var} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_var')?>">fuel_var()</a> function.</li>
	<li>{fuel_model('module_name', array(key="val"...)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_model')?>">fuel_model()</a> function. The first parameter is usually the name of your model and does not need to include the "_model" suffix (usually the same as the module name).</li>
	<li>{fuel_block(array(key="val"...))} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_block')?>">fuel_block()</a> function.</li>
	<li>{fuel_nav(array(key="val"...))} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_nav')?>">fuel_nav()</a> function.</li>
	<li>{fuel_edit(id, label, module, xOffset, yOffset)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper#func_fuel_edit')?>">fuel_edit()</a> function.</li>
</ul>
<p class="important">Note that several of the functions require an associative array paramter with the <dfn>key="val"</dfn> syntax.</p>

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
For example:
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