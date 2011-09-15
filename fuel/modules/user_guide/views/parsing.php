<h1>Parsing Overview</h1>
<p>By default, FUEL disables PHP code from being saved in your module data 
(you must change the <a href="<?=user_guide_url('modules/simple')?>">sanitize_input</a> setting for the module).
FUEL has implemented the <a href="http://dwoo.org/" target="_blank">Dwoo</a> PHP 5 based templating system.
</p>

<h2>MY_Parser</h2>
<p>FUEL overwrites the default CodeIgniter Parser library with the <a href="<?=user_guide_url('libraries/my_parser')?>">MY_Parser</a> 
library to implement the expanded Dwoo templating syntax.</p>


<h2>String Helper Functions</h2>
<p>FUEL comes with the following <a href="<?=user_guide_url('helpers/my_string_helper')?>">string helper functions</a> to help with parsing and converting from the Dwoo templating syntax:</p>
<ul>
	<li>php_to_template_syntax - Convert PHP syntax to <a href="http://dwoo.org" target="_blank">Dwoo templating syntax</a>. Must use the PHP alternative syntax for if and foreach loops to be translated correctly.</li>
	<li>parse_template_syntax - Parses a strings <a href="http://dwoo.org" target="_blank">Dwoo templating syntax</a>.</li>
</ul>

<h2>Non-Namespaced Functions</h2>
<p>The following are non-namespaced functions that can be used in your application and will be translated by the templating system.</p>
<ul>
	<li>{site_url('/my/path/')}</li>
	<li>{assets_path('images/my_asset.jpg')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper')?>">assets_path()</a> function</li>
	<li>{img_path('my_img.jpg')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper')?>">img_path()</a> function.</li>
	<li>{js_path('my_js.js')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper')?>">js_path()</a> function. The <dfn>.js</dfn> extension is optional.</li>
	<li>{swf_path('my_swf.swf')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper')?>">swf_path()</a> function. The <dfn>.swf</dfn> extension is optional.</li>
	<li>{media_path('my_movie.mov')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper')?>">media_path()</a> function.</li>
	<li>{pdf_path('my_pdf.pdf')} - Maps to the <a href="<?=user_guide_url('helpers/asset_helper')?>">pdf_path()</a> function. The <dfn>.pdf</dfn> extension is optional.</li>
	<li>{safe_mailto('my@email.com', 'text')} - Maps to the  <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">safe_mailto()</a> function.</li>
	<li>{redirect('my_redirect_page')} - Maps to the <a href="http://codeigniter.com/user_guide/helpers/url_helper.html" target="_blank">redirect()</a> function.</li>
	<li>{show_404} - Maps to the <a href="http://codeigniter.com/user_guide/general/errors.html" target="_blank">show_404()</a> function.</li>
</ul>

<h2>Namespaced Functions</h2>
<p>The following are namespaced functions that can be used in your application and will be translated by the templating system.</p>

<ul>
	<li>{uri_segment(1, true/false)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper')?>">uri_segment(n)</a> function.</li>
	<li>{fuel_var} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_var()</a> function.</li>
	<li>{fuel_model(model, array(key="val"...)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_modules()</a> function.</li>
	<li>{fuel_block(array(key="val"...))} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_block()</a> function.</li>
	<li>{fuel_nav(array(key="val"...))} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_nav()</a> function.</li>
	<li>{fuel_edit(id, label, module, xOffset, yOffset)} - Maps to the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_edit()</a> function.</li>
</ul>
<p class="important">Note that several of the functions require an associative array paramter with the <dfn>key="val"</dfn> syntax.</p>

<h2>Blocks</h2>
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
$my_data = $this->user_model->find_all();

{foreach $my_data user}
	{$user->name} - {$user->weapon}
{/foreach}
</pre>

<p class="important">The <dfn>user</dfn> part can actually be any value. You cannot insert outside variables inside a tag pair block.</p>

<h2>Conditional Statements</h2>
<p>FUEL also allows for php like conditional statements to be inserted into the view using <dfn>{if ... }</dfn>, <dfn>{elseif ...}</dfn> and <dfn>{/if}</dfn>.
For example:
</p>

<pre class="brush:php">
{$name='Darth Vader'}
{if $name == 'Darth Vader'}
I am your father.
{/if}
</pre>


<h2>Creating your own tags</h2>
<p><a href="http://wiki.dwoo.org/index.php/WritingPlugins" target="_blank">Dwoo provides several ways for it to extend it's templating syntax through plugins</a>. 
The Dwoo folder to add your plugins is located at <dfn>application/libraries/dwoo/plugins</dfn>.
</p>
