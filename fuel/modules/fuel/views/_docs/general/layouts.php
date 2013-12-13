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
<p>For a layout to appear in the FUEL admin, a layout view file must exist in the <span class="file">fuel/application/views/_layouts/</span> folder AND 
there must be an entry in the <span class="file">fuel/application/config/MY_fuel_layouts.php</span> file. The latter, assigns the variable fields to the layout and signals that the layout can be used to create pages in the admin.<p>

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

<h3>Layout Objects</h3>
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
<h3>Layouts As Their Own Class Files</h3>
<p>Similarly, you can overwite the <dfn>Base_layout</dfn> classes <dfn>fields()</dfn> method to return the fields for your layout:</p>
<span class="file">fuel/application/config/MY_fuel_layouts.php</span><br />
<pre class="brush:php">
$config['layouts']['home'] = array(
	'label' => 'Home',
	'filepath' => 'libraries/_layouts',
	'class' => 'Home_layout',
);

</pre>

<br />

<span class="file">fuel/application/libraries/_layouts/Home_layout.php </span><br />
<pre class="brush:php">
require_once('Base_layout.php');

class Home_layout extends Base_layout {

	// --------------------------------------------------------------------

	/**
	 * Fields specific to the home page
	 *
	 * @access	public
	 * @return	array
	 */	function fields()
	{
		$fields = parent::fields($include);
		// PUT YOUR FIELDS HERE...
		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Hook used for processing variables specific to a layout
	 *
	 * @access	public
	 * @param	array	variables for the view
	 * @return	array
	 */	
	function pre_process($vars)
	{
		return $vars;
	}

}

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

<h2 id="layouts_block_layouts">Block Layouts</h2>
<p>Block layouts are a way to associate layout form fields to particular blocks. This creates a whole new level of layout control for FUEL layouts. 
For example, you can now create repeatable sections in your layouts that allow you to select the block you want to use and will 
automatically pull in that block's associated fields. Block layouts are added to the <dfn>$config['blocks']</dfn> and <strong>NOT</strong>
<dfn>$config['layouts']</dfn>. Below is an example that creates a block field on a repeatable section. 
It assumes that you have a page layout of <span class="file">fuel/application/views/_layouts/test.php</span> and a block at <span class="file">fuel/application/views/_blocks/sections/image_right.php</span>:
</p>

<br />
<span class="file">fuel/application/config/MY_fuel_layouts.php</span></br >
<pre class="brush:php">
$test_layout = new Fuel_layout('test');
$test_layout->set_description('This is the test layout field.');
$test_layout->set_label('Test');

$test_fields = array(
	'Title/Intro'	=> array('type' => 'fieldset', 'label' => 'Title/Intro', 'class' => 'tab'),
	'h1' => array('label' => 'Heading'),
	'Sections'	=> array('type' => 'fieldset', 'label' => 'Sections', 'class' => 'tab'),
	'sections' => array(
					'type'			=> 'template',
					'display_label' => FALSE,
					'label' 		=> 'Sections', 
					'add_extra' 	=> FALSE,
					'repeatable'	=> TRUE,
					'max'			=> 4,
					'min'			=> 0,
					'title_field'   => 'block',
					'fields' 		=> array(
											'section'	=> array('type' => 'section', 'value' => 'Section &lt;span class=&quot;num&quot;&gt;{num}&lt;/span&gt;'),
											'block'		=> array('type' => 'block', 'folder' => 'sections'),
										),

					),

);
$test_layout->add_fields($test_fields);

// Added to the 'layouts' key
$config['layouts']['test'] = $test_layout;


// Fuel layout block
$image_right = new Fuel_block_layout('image_right');
$image_right->set_label('Image Right');
$image_right->add_field('title', array());
$image_right->add_field('content', array('type' =>'text'));
$image_right->add_field('image', array('type' =>'asset'));

// NOTE THIS IS ADDED TO THE 'blocks' key and not the 'layouts' key !!!!
$config['blocks']['image_right'] = $image_right;
</pre>

<br />
<span class="file">fuel/application/views/_layouts/test.php</span></br >
<pre class="brush:php">
	
&lt;?php $this->load->view('_blocks/header')?&gt;
	
	&lt;div id=&quot;main&quot;&gt;
		&lt;div class=&quot;wrapper&quot;&gt;
			
			&lt;article id=&quot;primary&quot;&gt;
				&lt;?=fuel_var(&#039;h1&#039;, &#039;&#039;); ?&gt;

				&lt;?php foreach($sections as $section) : 
					// 'block_name' contains the hidden field value that automatically set to the name of the selected block
					$block_name = $section[&#039;block&#039;][&#039;block_name&#039;];
					if (!empty($block_name)) :
				?&gt;
				&lt;?=fuel_block(&#039;sections/&#039;.$block_name, $section[&#039;block&#039;]) ?&gt;
				&lt;?php endif; ?&gt;
				&lt;?php endforeach; ?&gt;
			&lt;/article&gt;
		&lt;/div&gt;
	&lt;/div&gt;
	
&lt;?php $this->load->view('_blocks/footer')?&gt;


</pre>

<br />
<span class="file">fuel/application/views/_blocks/sections/image_right.php</span></br >
<pre class="brush:php">
&lt;section class=&quot;row&quot;&gt;
	&lt;div class=&quot;col_1-3&quot;&gt;&lt;img src=&quot;&lt;?=img_path($image)?&gt;&quot; /&gt;&lt;/div&gt;
	&lt;div class=&quot;col_2-3&quot;&gt;
		&lt;h3&gt;&lt;?=$title?&gt;&lt;/h3&gt;
		&lt;p&gt;&lt;?=$content?&gt;&lt;/p&gt;
	&lt;/div&gt;
&lt;/section&gt;
</pre>

<p>The value saved for block is an array that includes a special value of <dfn>block_name</dfn> for the name of the block selected.</p>
<pre class="brush:php">
Array
(
    [title] => My Title
    [content] => My Content
    [image] => my_image.jpg
    [block_name] => image_right
)
</pre>