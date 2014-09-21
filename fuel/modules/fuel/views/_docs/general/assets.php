<h1>Assets</h1>
<p>The Assets module allows you to manage the images, CSS, and javascript for your sites. You must set the asset folders you want managed
to have writable permissions. Because assets are saved on the file system and not in a database, you can use 
an FTP client to manage the assets as well.</p>

<h2>Uploading Assets</h2>
<p>To upload assets through the CMS, you can use the <dfn>Upload</dfn> button in the Assets module. The upload page provides the following upload options:</p>
<ul>
	<li><strong>File</strong>: The selected file(s) to upload</li>
	<li><strong>Asset Folder</strong>: The asset folder to upload to</li>
	<li><strong>New file name</strong>: The file name you want to assign to the uploaded file. The default is it's current name</li>
	<li><strong>Subfolder</strong>: A subfolder name within the selected asset folder to upload to. It will automatically create the folder if it doesn't exist</li>
	<li><strong>Overwrite</strong>: Determines whether to overwrite an image or create a new one</li>
	<li><strong>Create thumb</strong>: Creates a thumbnail of the uploaded image (Image Specific)</li>
	<li><strong>Maintain ratio</strong>: Determines whether to maintain the aspect ratio of the image if resized (Image Specific)</li>
	<li><strong>Width</strong>: The thumbnails width (Image Specific)</li>
	<li><strong>Height</strong>: The thumbnails height (Image Specific)</li>
	<li><strong>Master dimension</strong>: The master dimension to use when creating the thumbnail (Image Specific)</li>
</ul>

<h3>Incorporating Into Your Own Module</h3>
<p>You can incorporate assets into your own modules by specifying a field type of <a href="#">asset</a> in your model's <dfn>form_fields</dfn> method.
This will actually open up the assets module in a window for you to upload one or more files.
This field type provides a couple parameters to control things including:</p>
<ul>
	<li><strong>folder</strong>: The asset folder to upload to</li>
	<li><strong>file_name</strong>: The file name you want to assign to the uploaded file. The default is it's current name</li>
	<li><strong>subfolder</strong>: A subfolder name within the selected asset folder to upload to. It will automatically create the folder if it doesn't exist</li>
	<li><strong>overwrite</strong>: Determines whether to overwrite an image or create a new one</li>
	<li><strong>create_thumb</strong>: Creates a thumbnail of the uploaded image (Image Specific)</li>
	<li><strong>maintain_ratio</strong>: Determines whether to maintain the aspect ratio of the image if resized (Image Specific)</li>
	<li><strong>width</strong>: The thumbnails width (Image Specific)</li>
	<li><strong>height</strong>: The thumbnails height (Image Specific)</li>
	<li><strong>master_dim</strong>: The master dimension to use when creating the thumbnail (Image Specific)</li>
</ul>

<pre class="brush:php">
function form_fields($values = array(), $related = array())
{
	$fields = parent::form_fields($values, $related);
	$fields['image_field'] = array('type' => 'asset', 'folder' => 'my_folder', 'overwrite' => FALSE);
	return $fields;
}
</pre>

<p class="important">Field names that end with "img" or "image" will automatically be assigned a field type of <dfn>asset</dfn>.</p>


<h2>Using Assets in Pages</h2>
<p>To incorporate assets in your pages, use one of the <a href="<?=user_guide_url('helpers/asset_helper')?>">asset_helper</a> functions like
<a href="<?=user_guide_url('helpers/asset_helper#func_js')?>">js()</a>, <a href="<?=user_guide_url('helpers/asset_helper#func_css')?>">css()</a> or <a href="<?=user_guide_url('helpers/asset_helper#func_img_path')?>">img_path</a>. Below are some examples:</p>
<pre class="brush:php">
// using normal PHP syntax if used in a static view file 
&lt;img src="&lt;?php echo img_path('my_img.jpg')?&gt;" alt="My Image" /&gt;

// using the templating syntax if used in field in the CMS
&lt;img src="{img_path('my_img.jpg')}" alt="My Image" /&gt;
</pre>

<h2>Optimizing Assets</h2>
<p>FUEL provides a way to optimize JS and CSS files by condensing their output into a single file. This can be done "on the fly" by changing the 
<dfn>assets_output</dfn> configuration parameter to TRUE (or one of the other values listed in the configuration parameters comment).
This will combine all the files called in a single <a href="<?=user_guide_url('helpers/asset_helper#func_js')?>">js()</a> and <a href="<?=user_guide_url('helpers/asset_helper#func_css')?>">css()</a> 
function call and optimize them by removing whitespace and adding gzip compression.
</p>
<p class="important">Must have the assets/cache/ folder writable for the assets_output compression to work "on the fly".</p>
<br />
<p>Alternatively, you can generate these files via command line by using FUEL's "build" functionality like so:</p>
<pre class="brush:php">
// js
&gt;php index.php fuel/build/app/js/plugins:plugins

// css
&gt;php index.php fuel/build/app/css/plugins:plugins
</pre>
<p>The above will grab all the javascript files located in the main <span class="file">assets/js/plugins</span> folder (the segment "app" refers to the main assets directory) and create a file called <span class="file">plugins.js</span> in the <span class="file">assets/js</span> folder.
The file name is denoted after the colon. If no file name is provided then it will default to <span class="file">main.min.js</span> or <span class="file">main.min.css</span> respectively.
</p>