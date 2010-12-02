<h1>Layouts</h1>
<p>Layouts in FUEL serve as variable placeholders for your pages. They currently
only exist as view files and cannot be changed in the FUEL admin interface.
You can use layouts for both your static view files as well as pages you
create inside FUEL. Layout files should be placed in the <dfn>application/views/_layouts/</dfn>
folder.
</p>

<h2>Using Layouts for Static View Files</h2>
<p>If you'd like to use a layout for your view files, you should assign a 
<a href="<?=user_guide_url('general/opt-in-controllers')?>">view variable</a> with the key of 
<dfn>layout</dfn> in your variables file or define a variable of <dfn>layout</dfn> using <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_set_var</a>.
</p>

<pre class="brush:php">
// in your _variables/home.php
$vars['layout'] = 'home';

// or as a page variable in the same file
$pages['home'] = array('layout' => '_layouts/home');


// using the fuel_set_var in your view file
&lt;?php fuel_set_var('layout', '_layouts/home')?&gt;
</pre>

<p class="important">You should include the <kbd>_layouts</kbd> folder in the path to the layout.</p>


<h2>Using Layouts for FUEL Pages</h2>
<p>If you want to add a layout for your FUEL pages to use (those altered in the admin), you will need to 
alter the <dfn>application/config/MY_fuel_layouts.php</dfn> file. You will need to add the name of the layout file 
(or an array of names) to the <dfn>layouts</dfn> configuration. After that, you will need to specify the 
<dfn>layout_fields</dfn> which is a multi-demensional array that maps to the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a>.
</p>

<pre class="brush:php">
$config['layouts']['main'] = $config['layouts_path'].'main';

// main layout fields
$config['layout_fields']['main'] =  array(
	'page_title' => '',
	'meta_description' => '',
	'meta_keywords' => '',
	'body' => array('type' => 'textarea', 'description' => 'Main content of the page'),
	'body_class' => ''
	);

</pre>