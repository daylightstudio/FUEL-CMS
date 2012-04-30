<h1>Views</h1>
<p>Views within FUEL work just like <a href="http://codeigniter.com/user_guide/general/views.html" target="_blank">CodeIgniter views</a> but with some extra functionality. 
For example, you can render a page automatically without creating a controller method by using the <a href="<?=user_guide_url('introduction/opt-in-controllers')?>">opt-in controller method</a>.</p>

<h2>Hiding Views</h2>
<p>You can hide view files from several aspects of FUEL by adding an underscore (<strong>"_"</strong>) in front of the view name or containing folder. This will automatically hide it from being found
if you are using the <a href="<?=user_guide_url('introduction/opt-in-controllers')?>">opt-in controller method</a> for displaying your page. Also, adding an underscore to your
layout or block view file (in the _layouts and _blocks folder respectively), will prevent it from showing up in the selection dropdown list in the CMS.</p>

<h2>Special View Folders</h2>
<p>There are several folders that have special purpose within your views folders:</p>
<ul>
	<li><strong>_admin</strong>: contains CMS admin specific views. The application folder uses this folder to hold the <dfn>_fuel_preview.php</dfn> 
	file which is used for previewing content from the markItUp! editor. You may need to customize to fit your preview needs</li>
	<li><strong>_blocks</strong>: contains static <a href="<?=user_guide_url('general/blocks')?>">block</a> files.</li>
	<li><strong>_docs</strong>: contains <a href="<?=user_guide_url('general/blocks')?>">user guide</a> documentation specific for a module. The application folder's _doc folder is used for site specific documentation which
	can be viewed from the CMS dashboard</li>
	<li><strong>_layouts</strong>: contains <a href="<?=user_guide_url('general/layouts')?>">layout files</a></li>
	<li><strong>_variables</strong>: contains <a href="<?=user_guide_url('introduction/opt-in-controllers')?>">variables files</a></li>
	<li><strong>_generate</strong>: contains template files that override the defaults used for generating things like advanced modules. This folder is not there by default and the generate
	functionality will work without it.</li>
</ul>
