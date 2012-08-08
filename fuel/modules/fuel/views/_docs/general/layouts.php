<h1>Layouts</h1>
<p>Layouts in FUEL are made up of static content and variable placeholders to be used by your pages. You can use layouts for both your <a href="#layouts_static_views">static view files</a> as well as pages you
create inside the <a href="#layouts_cms">CMS</a>. Layout files should be placed in the <span class="file">application/views/_layouts/</span> folder.</p>

<p>Programmatically, you can access information about the layouts through the instantiated <a href="<?=user_guide_url('libraries/fuel_layouts')?>">Fuel_layouts</a> object like so:</p>
<pre class="brush:php">
$this->fuel->layouts->options_list();
$this->fuel->layouts->get();
</pre>


<p class="important">Layouts currently only exist as view files and cannot be changed in the CMS.</p>



<h2 id="layouts_static_views">Using Layouts for Static View Files</h2>
<p>There are a couple ways to assign a layout to a static view file. The first is to assign a
<a href="<?=user_guide_url('general/opt-in-controllers')?>">view variable</a> with the key of 
<dfn>layout</dfn> in your variables file. The second is to assign a variable of <dfn>layout</dfn> using <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_set_var</a>.
</p>

<pre class="brush:php">
// in your _variables/home.php
$vars['layout'] = 'home';

// or as a page variable in the same file
$pages['home'] = array('layout' => 'home');


// using the fuel_set_var in your view file
&lt;?php fuel_set_var('layout', 'home')?&gt;
</pre>

<p class="important">The variable <dfn>$body</dfn> is is used to hold the contents of the static view file which gets merged into the layout. 
	Because of this, you'll often see <dfn>fuel_set_var('body', '')</dfn> in layout files.</p>

<h2 id="layouts_cms">Using Layouts in the CMS</h2>
<p>For a layout to appear in the FUEL admin, a layout view file must exist in the <span class="file">application/views/_layouts/</span> folder AND 
there must be an entry in the <span class="file">application/config/MY_fuel_layouts.php</span> file. The latter, assigns the variable fields to the layout and signals that the layout can be used to create pages in the admin.<p>

<p>As of version 1.0, there are two ways to assign layouts in the <span class="file">MY_fuel_layouts.php</span> file. The first being the old way of assigning the layout variables to the 
<dfn>fields</dfn> key which is a multi-demensional array that will be used by <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder</a>.</p>

<p class="important">Note that there was a change in version 1.0 that all layout information exists under a single key in the <dfn>$config['layouts']</dfn> array and all fields now exist as a child under the "fields" key.</p>

<pre class="brush:php">
$config['layouts']['main'] = array(
	'fields'	=> array(
		'Header' => array('type' => 'fieldset', 'label' => 'Header', 'class' => 'tab'),
		'page_title' => array('label' => lang('layout_field_page_title')),
		'heading' => array('label' => 'heading'),
		'meta_description' => array('label' => lang('layout_field_meta_description')),
		'meta_keywords' => array('label' => lang('layout_field_meta_keywords')),
		'Body' => array('type' => 'fieldset', 'label' => 'Body', 'class' => 'tab'),
		'body' => array('label' => lang('layout_field_body'), 'type' => 'textarea', 'description' => lang('layout_field_body_description')),
		'body_class' => array('label' => lang('layout_field_body_class')),
	)
);
</pre>

<p>The second, and newer way, is to create a <a href="<?=user_guide_url('libraries/fuel_layouts#fuel_layout')?>">Fuel_layout</a> object and assign the layout variables to it. Additionally, you can assign the <dfn>label</dfn> (used for the dropdown in the admin), 
and the <dfn>description</dfn> (used to describe the layout in the admin). For layouts that share common fields, we recommend separating them out and assigning them using the <dfn>add_fields</dfn> method which adds multiple fields at once.
Note that you must still assign the object to the <dfn>$config['layouts']['main']</dfn> below:
</p>
<pre class="brush:php">
$common_meta = array(
	'Meta' => array('type' => 'fieldset', 'label' => 'Meta', 'class' => 'tab'),
	'meta_section' => array('type' => 'copy', 'label' => 'The following fields control the meta information found in the head of the HTML.'),
	'page_title' => array('style' => 'width: 520px', 'label' => lang('layout_field_page_title')),
	'meta_description' => array('style' => 'width: 520px', 'label' => lang('layout_field_meta_description')),
	'meta_keywords' => array('style' => 'width: 520px', 'label' => lang('layout_field_meta_keywords')),
);

$main_layout = new Fuel_layout('main');
$main_layout->set_description('This is the main layout used for most of the pages of the site.');
$main_layout->set_label('Main');
$main_layout->add_fields($common_meta);
$main_layout->add_field('Sections',  array('type' => 'fieldset', 'label' => 'Sections', 'class' => 'tab'));
$main_layout->add_field('section_description', array('type' => 'copy', 'label' => 'The following fields control the sections of the legal page.'));
$main_layout->add_field('sections', array('display_label' => FALSE, 'add_extra' => FALSE, 'init_display' => 'none', 'dblclick' => 'accordian', 'repeatable' => TRUE, 'style' => 'width: 900px;', 'type' => 'template', 'label' => 'Page sections', 'title_field' => 'title',
											'fields' => array(
												'sections' => array('type' => 'section', 'label' => '{__title__}'),
												'title' => array('style' => 'width: 800px'),
												'content' => array('type' => 'textarea', 'style' => 'width: 800px; height: 500px;'),
											)));
// !!! IMPORTANT ... NOW ASSIGN THIS TO THE MAIN "layouts"
$config['layouts']['main'] = $main_layout;
</pre>

<p class="important"><strong>What is all this other stuff in the "Sections area"?</strong> We added this to the example just to show how complicated you can get with the layouts. The above is using 
several new features including the addition of the class "tabs" which can be applied to a "type" of "fieldset" and creates tabs for your forms automatically. We are also
displaying the new field type of "template", which gives you the power to easilty create complicated repeatable forms that you can reorder and nest inside other forms. 
For more details visit the <a href="#">Form_builder</a> page.</p>


<h2 id="layouts_custom_classes">Custom Layout Classes</h2>
<p>Occasionally, you may need a layout that does some additional variable processing before being applied to the page. To accomplish this, you can create your own 
Layout class by extending the <a href="<?=user_guide_url('libraries/fuel_layouts#fuel_layout')?>">Fuel_layout</a> class and modifying the <dfn>pre_process</dfn> and/or <dfn>post_process</dfn> method hooks. The <dfn>pre_process</dfn> 
method gets passed the array of variables used to generate the page (e.g. formatting of dates). 
The <dfn>post_process</dfn> method gets passed the final rendered output in case you want to do any post rendering of the final output (e.g. appending tracking scripts).
You can tell FUEL where to look for the class by specifying <dfn>file</dfn>, <dfn>class</dfn>, <dfn>filepath</dfn> and <dfn>filename</dfn> parameters as displayed below:
</p>

<pre class="brush:php">
$config['layouts']['main'] = array(
	'class'		=> 'Main_layout',
	'filepath' => 'libraries',
	'filename' => 'Main_layout.php',
	'fields'	=> array(
		'Header' => array('type' => 'fieldset', 'label' => 'Header', 'class' => 'tab'),
		'page_title' => array('label' => lang('layout_field_page_title')),
		'heading' => array('label' => 'heading'),
		'meta_description' => array('label' => lang('layout_field_meta_description')),
		'meta_keywords' => array('label' => lang('layout_field_meta_keywords')),
		'Body' => array('type' => 'fieldset', 'label' => 'Body', 'class' => 'tab'),
		'body' => array('label' => lang('layout_field_body'), 'type' => 'textarea', 'description' => lang('layout_field_body_description')),
		'body_class' => array('label' => lang('layout_field_body_class')),
	)
);
</pre>

<h2 id="layouts_different_layout">Assigning a Different Layout View</h2>
<p>By default, FUEL will assume the layout view file is the same name as the layout's key value (e.g. 'main'). To change it, you can assign the layout a different <dfn>file</dfn> value:</p>
<pre class="brush:php">
$config['layouts']['main'] = array(
	'file' 		=> $config['layouts_path'].'MY_main_layout',
	'class'		=> 'Main_layout',
	'filepath' => 'libraries',
	'filename' => 'Main_layout.php',
	'fields'	=> array(
		'Header' => array('type' => 'fieldset', 'label' => 'Header', 'class' => 'tab'),
		'page_title' => array('label' => lang('layout_field_page_title')),
		'heading' => array('label' => 'heading'),
		'meta_description' => array('label' => lang('layout_field_meta_description')),
		'meta_keywords' => array('label' => lang('layout_field_meta_keywords')),
		'Body' => array('type' => 'fieldset', 'label' => 'Body', 'class' => 'tab'),
		'body' => array('label' => lang('layout_field_body'), 'type' => 'textarea', 'description' => lang('layout_field_body_description')),
		'body_class' => array('label' => lang('layout_field_body_class')),
	)
);
</pre>
