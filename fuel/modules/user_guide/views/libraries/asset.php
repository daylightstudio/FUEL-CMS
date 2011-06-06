<h1>Asset Class</h1>

<p>The Asset class gives you a way to manage paths to asset files in your web project. 
It also provides several convenient methods for embedding <kbd>css</kbd>, <kbd>javascript</kbd> and <kbd>flash</kbd> assets.
</p>

<p class="important">This class must exist in the application libraries folder for the FUEL CMS admin to work</p>

<h2>Initializing the Class</h2>

<p>Like most other classes in CodeIgniter, the Asset class is initialized in your controller using the <dfn>$this->load->library</dfn> function:</p>

<pre class="brush: php">$this->load->library('asset');</pre>

<p>Alternatively, you can pass initialization parameters as the second parameter:</p>

<pre class="brush: php">$this->load->library('asset', array('assets_path' => 'external_files', 'assets_last_updated' => '01/01/2010 00:00:00'));</pre>

<p>Once loaded, the Asset object will be available using: <dfn>$this->asset</dfn>. 
Additionally, you can use the <a href="<?=user_guide_url('helpers/asset')?>">asset helper</a>
which provides a shortcut for many of the methods of the asset class.
</p>

<h2>Configuring Asset Information</h2>
<p>The Asset class automatically loads the <dfn>config/asset.php</dfn> file to initialize. That file contains the following
configurable parameters:
</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td><strong>assets_path</strong></td>
			<td>NULL</td>
			<td>None</td>
			<td>The web path to the main assets folder relative base web folder (where the index.php bootstrap file exists)</td>
		</tr>
		<tr>
			<td><strong>assets_module_path</strong></td>
			<td>fuel/modules/{module}/assets/</td>
			<td>None</td>
			<td>The path to module asset files. Use <dfn>{module}</dfn> as a placeholder for the module name</td>
		</tr>
		<tr>
			<td><strong>assets_server_path</strong></td>
			<td>WEB_ROOT.$config['assets_path'];</td>
			<td>None</td>
			<td>The full server path to the main assets folder</td>
		</tr>
		<tr>
			<td><strong>assets_module</strong></td>
			<td>None</td>
			<td>None</td>
			<td>The module assets folder to use</td>
		</tr>
		<tr>
			<td><strong>assets_folders</strong></td>
			<td><pre>
array(
'images' => 'images/',
'css' => 'css/',
'js' => 'js/',
'pdf' => 'pdf/',
'swf' => 'swf/',
'media' => 'media/',
'captchas' => 'captchas/'
);
				</pre></td>
			<td>Any subfolder in the assets folder</td>
			<td>The main asset subfolders including images, css, and js files</td>
		</tr>
		<tr>
			<td><strong>assets_absolute_path</strong></td>
			<td>FALSE</td>
			<td>TRUE/FALSE (boolean)</td>
			<td>A boolean value to determine of the asset paths have the full path including the <dfn>http://www.mywebsite</dfn></td>
		</tr>
		<tr>
			<td><strong>assets_last_updated</strong></td>
			<td>00/00/0000 00:00:00</td>
			<td></td>
			<td>Used for caching. Change this date to break the caching of files. Good for updating files with the same name that may be cached on the server. Will append a query string parameter to the end of the file name if used.</td>
		</tr>
		<tr>
			<td><strong>asset_append_cache_timestamp</strong></td>
			<td>array('js', 'css')</td>
			<td>js, css, images, pdf, swf</td>
			<td>The asset types that should include the cache timestamp. Default is <dfn>css</dfn> and <dfn>js</dfn></td>
		</tr>
		<tr>
			<td><strong>assets_output</strong></td>
			<td>FALSE</td>
			<td>
			<ul>
				<li><dfn>FALSE</dfn> - no optimization</li>
				<li><dfn>TRUE</dfn> - will combine files, strip whitespace, and gzip</li>
				<li><dfn>inline</dfn> - will render the files inline</li>
				<li><dfn>gzip</dfn> - will combine files (if multiple) and gzip without stripping whitespace</li>
				<li><dfn>whitespace</dfn> - will combine files (if multiple) and strip out whitespace without gzipping</li>
				<li><dfn>combine</dfn> - will combine files (if multiple) but will not strip out whitespace or gzip</li>
			</ul>
			</td>
			<td>Different methods to output and optimize css and js files. See the next section about optimizing output for more information.</td>
		</tr>
		<tr>
			<td><strong>assets_cache_folder</strong></td>
			<td>cache</td>
			<td>None</td>
			<td>The folder name to put asset cache files when <dfn>assets_output</dfn> has a an optimization value</td>
		</tr>
		<tr>
			<td><strong>assets_gzip_cache_expiration</strong></td>
			<td>3600</td>
			<td>None</td>
			<td>The experiation date set in the http header if the assets_output value is set to <dfn>TRUE</dfn> or <dfn>zip</dfn></td>
		</tr>
		
	</tbody>
</table>

<h2>Optimizing Assets</h2>
<p>You can increase server performance by optimizing your assets. The <dfn>assets_output</dfn> configuration parameter explained above
gives you several ways to output and optimize your css and js files. Values of <dfn>TRUE</dfn>, <dfn>gzip</dfn>, <dfn>whitespace</dfn> and <dfn>combine</dfn>
will put files in the assets cache folder.
</p>
<p class="important"><strong>Important</strong> - The cache folder must have writable permissions.</p>

<h2>Manage Assets in the FUEL Admin</h2>
<p>FUEL CMS can help users manage asset files for their site. More information about managing assets can be found in the <a href="<?=user_guide_url('modules/fuel/assets')?>">asset modules</a> documentation</p>
<p class="important"><strong>Important</strong> - To allow the FUEL Admin to manage your sites assets, you must give the folder(s) writable permissions.</p>


<br />

<h1>Function Reference</h1>
<p>The following functions return paths to asset files.
The first parameter is the name of the asset file. 
The second parameter is used when you want to reference an asset folder in a module. 
The third parameter provides an http absolute path.
If no parameters are passed then just the web path to the respective asset folder will be returned.
Each path function below has a corresponding helper function from the <a href="<?=user_guide_url('helpers/asset')?>">asset_helper</a> for convenience.
</p>
<p class="important">If the asset type is listed in the <kbd>asset_append_cache_timestamp</kbd> configuration parameter, a query string 
parameter similar to this <strong>?c=943948800</strong> will be appended to the end of the file name.</p>



<a name="img_path"></a>
<h2>$this->asset->img_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to an image asset. 
This and the helper function equivalent are commonly used in <kbd>&lt;img&gt;</kbd> tags.
The first parameter is the name of the image file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->img_path('banner.jpg');
// /assets/images/banner.jpg

echo $this->asset->img_path('banner.jpg', 'my_module');
// /fuel/modules/my_module/assets/images/banner.jpg (assuming /fuel/modules is where the module folder is located)

echo $this->asset->img_path('banner.jpg', NULL, TRUE);
// http://www.mysite.com/assets/images/banner.jpg (if the "assets_module" Asset class property is empty)
// http://www.mysite.com/fuel/modules/my_module/assets/images/banner.jpg (if the "assets_module" Asset class property is my_module)

echo $this->asset->img_path('banner.jpg', '', TRUE);
// http://www.mysite.com/assets/images/banner.jpg (and empty string for the module parameter will properly ignore anything in the assets_module Asset class property)

</pre>
<p class="important">File extension <strong>must</strong> be included.</p>



<a name="css_path"></a>
<h2>$this->asset->css_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to a css stylesheet asset. 
This and the helper function equivalent are commonly used in css <kbd>&lt;link&gt;</kbd> tags.
The first parameter is the name of the css file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->css_path('main');
// /assets/css/main.css

echo $this->asset->css_path('main', 'my_module');
// /fuel/modules/my_module/assets/css/main.css (assuming /fuel/modules is where the module folder is located)

echo $this->asset->css_path('main', NULL, TRUE);
// http://www.mysite.com/assets/css/main.css
</pre>

<p class="important">The <kbd>.css</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>



<a name="js_path"></a>
<h2>$this->asset->js_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to a javascript asset. 
This and the helper function equivalent are commonly used in javascript <kbd>&lt;script&gt;</kbd> tags.
The first parameter is the name of the javascript file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->js_path('main');
// /assets/js/main.js

echo $this->asset->js_path('main', 'my_module');
// /fuel/modules/my_module/assets/js/main.js (assuming /fuel/modules is where the module folder is located)

echo $this->asset->js_path('main', NULL, TRUE);
// http://www.mysite.com/assets/js/main.js
</pre>

<p class="important">The <kbd>.js</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>



<a name="swf_path"></a>
<h2>$this->asset->swf_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to a flash swf asset. 
This and the helper function equivalent are commonly used in <kbd>&lt;object&gt;</kbd> and <kbd>&lt;embed&gt;</kbd> tags.
The first parameter is the name of the swf file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->swf_path('main');
// /assets/swf/home.swf

echo $this->asset->swf_path('main', 'my_module');
// /fuel/modules/my_module/assets/swf/home.swf (assuming /fuel/modules is where the module folder is located)

echo $this->asset->swf_path('main', NULL, TRUE);
// http://www.mysite.com/assets/swf/home.swf
</pre>

<p class="important">The <kbd>.swf</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>



<a name="pdf_path"></a>
<h2>$this->asset->pdf_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to a pdf asset. 
This and the helper function equivalent are commonly used in anchor (<kbd>&lt;a&gt;</kbd>) tags linking to pdf files.

The first parameter is the name of the pdf file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->pdf_path('newsletter');
// /assets/swf/newsletter.pdf

echo $this->asset->pdf_path('main', 'my_module');
// /fuel/modules/my_module/assets/pdf/newsletter.pdf (assuming /fuel/modules is where the module folder is located)

echo $this->asset->pdf_path('main', NULL, TRUE);	
// http://www.mysite.com/assets/pdf/newsletter.pdf
</pre>

<p class="important">The <kbd>.pdf</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>



<a name="media_path"></a>
<h2>$this->asset->media_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to a media asset (e.g. a quicktime movie).
This and the helper function equivalent are commonly used in anchor (<kbd>&lt;a&gt;</kbd>) tags linking to media files.

The first parameter is the name of the media file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->media_path('mymovie.mov');
// /assets/swf/newsletter.pdf

echo $this->asset->media_path('main', 'my_module');
// /fuel/modules/my_module/assets/swf/newsletter.pdf (assuming /fuel/modules is where the module folder is located)

echo $this->asset->media_path('main', NULL, TRUE);
// http://www.mysite.com/assets/swf/newsletter.pdf
</pre>

<p class="important">File extensions <strong>must</strong> be included.</p>



<a name="cache_path"></a>
<h2>$this->asset->cache_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to the cache folder for optimized javascript and css assets.
The configuration parameter <kbd>assets_output</kbd> must be set to either 
<dfn>TRUE</dfn>, <dfn>gzip</dfn>, <dfn>whitespace</dfn> and <dfn>combine</dfn> for 
files to appear in this folder.

The first parameter is the name of the media file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->cache_path('3c38643da81c3cee289feac34465c353_943948800.php');
// /assets/cache/3c38643da81c3cee289feac34465c353_943948800.php

echo $this->asset->cache_path('3c38643da81c3cee289feac34465c353_943948800.php', 'my_module');
// /fuel/modules/my_module/assets/cache/3c38643da81c3cee289feac34465c353_943948800.php (assuming /fuel/modules is where the module folder is located)

echo $this->asset->cache_path('3c38643da81c3cee289feac34465c353_943948800.php', NULL, TRUE);
// http://www.mysite.com/assets/cache/3c38643da81c3cee289feac34465c353_943948800.php
</pre>

<p class="important">File extensions <strong>must</strong> be included. 
Modules should include a <strong>writable</strong> asset cache folder (e.g. assets/cache) if asset optimizing is used
</p>



<a name="captcha_path"></a>
<h2>$this->asset->captcha_path(<var>['file']</var>, <var>['module']</var>, <var>[is_absolute]</var>)</h2>
<p>Returns the web path to where captcha images are stored. 
The captcha plugin (or another captcha library), should use this folder to store
captcha images.

The first parameter is the name of the capthca file. 
The second parameter module folder name.
The third parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->captcha_path('123456_captcha.jpg');
// /assets/captcha/123456_captcha.jpg

echo $this->asset->captcha_path('123456_captcha.jpg', 'my_module');
// /fuel/modules/my_module/assets/captcha/123456_captcha.jpg (assuming /fuel/modules is where the module folder is located)

echo $this->asset->captcha_path('123456_captcha.jpg', NULL, TRUE);
// http://www.mysite.com/assets/captcha/123456_captcha.jpg
</pre>

<p class="important">File extensions <strong>must</strong> be included.
This folder must be <strong>writable</strong>.
</p>



<a name="assets_path"></a>
<h2>$this->asset->assets_path(<var>'file'</var>, <var>'type'</var>, <var>'module'</var>, <var>is_absolute</var>)</h2>
<p>Returns the web path to an asset file.

The first parameter is the name of the asset file. 
The second parameter is the subfolder name (e.g. images).
The third parameter is the module folder name.
The fourth parameter is a boolean value to return either a relative or an http absolute path.
Examples:
</p>

<pre class="brush: php">
echo $this->asset->assets_path();
// /assets/

echo $this->asset->assets_path('banner.jpg', 'images');
// /assets/images/banner.jpg

echo $this->asset->assets_path('banner.jpg', 'images', 'my_module');
// /fuel/modules/my_module/assets/images/banner.jpg (assuming /fuel/modules is where the module folder is located)

echo $this->asset->assets_path('banner.jpg', 'images', NULL, TRUE);
// http://www.mysite.com/assets/images/banner.jpg
</pre>

<p class="important">File extensions <strong>must</strong> be included.
This folder must be <strong>writable</strong>.
</p>



<a name="assets_server_path"></a>
<h2>$this->asset->assets_server_path(<var>'file'</var>, <var>'type'</var>, <var>'module'</var>)</h2>
<p>Returns the server path to an asset file.

The first parameter is the name of the asset file. 
The second parameter is the asset type subfolder name (e.g. images).
The third parameter is the module folder name. Examples:
</p>

<pre class="brush: php">
echo $this->asset->assets_server_path();
// /Library/WebServer/Documents/assets/

echo $this->asset->assets_path('banner.jpg', 'images');
// /Library/WebServer/Documents/assets/images/banner.jpg

echo $this->asset->assets_path('banner.jpg', 'images', 'my_module');
// /Library/WebServer/Documents/fuel/modules/my_module/assets/images/banner.jpg (assuming /fuel/modules is where the module folder is located)
</pre>

<p class="important">File extensions <strong>must</strong> be included.
This folder must be <strong>writable</strong>.
</p>



<a name="assets_server_to_web_path"></a>
<h2>$this->asset->assets_server_to_web_path(<var>'file'</var>, <var>truncate_to_asset_folder</var>)</h2>
<p>Converts a server path to a web path. Typically used when you scan a directory of files and you need to convert from the server path to the web path.
The <dfn>truncate_to_asset_folder</dfn> will truncate the path all the way to the particular asset folder.
</p>

<pre class="brush: php">
$file_server_path = '/Library/WebServer/Documents/assets/images/my_img.jpg';
echo $this->asset->assets_server_to_web_path($file_server_path);
// /assets/images/my_img.jpg
</pre>




<a name="js"></a>
<h2>$this->asset->js(<var>'file'</var>, <var>['module']</var>, <var>[params]</var>)</h2>
<p>Can return either be a <kbd>&lt;script&gt;</kbd> tag with a <strong>src</strong> value pointing to a file or can print the contents of that file inlne.
The first parameter is the path to the file(s). Use a comma seperated string or an array to specify multiple files.
The second parameter is the module name.
The third parameter is an <kbd>array</kbd> of additional attributes to pass. Those attributes can be the following
</p>
<ul>
	<li><strong>attrs</strong> - additional attributes to pass to the <kbd>&lt;script&gt;</kbd> tag. Can be a string or an array</li>
	<li><strong>output</strong> - the output method to be applied to the contents of the file. Can be any of the <kbd>assets_output</kbd></li>
	<li><strong>ie_conditional</strong> - applies an IE specific conditional comment around the <kbd>&lt;script&gt;</kbd> tag</li>
</ul>

<p>Additionally, if the asset configuration of <kbd>asset_append_cache_timestamp</kbd> includes <strong>js</strong>,
then the caching timestamp will be appended as a query string parameter at the end just like if you were to use
<kbd>$this->asset->js_path().</kbd>
Examples:
</p>

<pre class="brush: php">
$this->asset->js('main');
// &lt;script src="/assets/js/main.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

echo $this->asset->js('jquery, main');
// &lt;script src="/assets/js/jquery.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;
// &lt;script src="/assets/js/main.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

echo $this->asset->js(array('jquery', 'main'));
// &lt;script src="/assets/js/jquery.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;
// &lt;script src="/assets/js/main.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

echo $this->asset->js('main', 'my_module');
// &lt;script src="/fuel/modules/my_module/assets/js/jquery.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

echo $this->asset->js('main', NULL, array('output' => TRUE, 'attrs' => 'onload=myOnloadFunc()', 'ie_conditional' => 'lte IE 6'));
// &lt;!--[if lte IE 6]&gt;
// &lt;script src="/assets/cache/3c38643da81c3cee289feac34465c353_943948800.php" type="text/javascript" charset="utf-8" onload="myOnloadFunc"&gt;&lt;/script&gt;
// &lt;![endif]--&gt;
</pre>

<p class="important"><strong>Important</strong> - All path references in the javascript file (e.g. paths to image files), should be changed to absolute if the script is printed <strong>inline</strong>.</p>


<a name="css"></a>
<h2>$this->asset->css(<var>'file'</var>, <var>['module']</var>, <var>[params]</var>)</h2>
<p>Can return either be a <kbd>&lt;link&gt;</kbd> tag with a <strong>href</strong> value pointing to a file or can print the contents of that file inlne in a <kbd>style</kbd> tag.
The first parameter is the path to the file(s). Use a comma seperated string or an array to specify multiple files.
The second parameter is the module name.
The third parameter is an <kbd>array</kbd> of additional attributes to pass. Those attributes can be the following
</p>
<ul>
	<li><strong>attrs</strong> - additional attributes to pass to the <kbd>&lt;link&gt;</kbd> tag. Can be a string or an array</li>
	<li><strong>output</strong> - the output method to be applied to the contents of the file. Can be any of the <kbd>assets_output</kbd></li>
	<li><strong>ie_conditional</strong> - applies an IE specific conditional comment around the <kbd>&lt;link&gt;</kbd> tag</li>
</ul>

<p>Additionally, if the asset configuration of <kbd>asset_append_cache_timestamp</kbd> includes <strong>css</strong>,
then the caching timestamp will be appended as a query string parameter at the end just like if you were to use
<kbd>$this->asset->css_path().</kbd>
Examples:
</p>

<pre class="brush: php">
echo $this->asset->css('main');
// &lt;link href="/assets/css/main.css" media="all" rel="stylesheet"/&gt;

echo $this->asset->css('main, home');
// &lt;link href="/assets/css/main.css" media="all" rel="stylesheet"/&gt;
// &lt;link href="/assets/css/home.css" media="all" rel="stylesheet"/&gt;

echo $this->asset->js(array('main', 'home'));
// &lt;link href="/assets/css/main.css" media="all" rel="stylesheet"/&gt;
// &lt;link href="/assets/css/home.css" media="all" rel="stylesheet"/&gt;

echo $this->asset->css('main', 'my_module');
// &lt;link href="fuel/modules/my_module/assets/css/main.css" media="all" rel="stylesheet"/&gt;

echo $this->asset->css('main', NULL, array('output' => TRUE, 'attrs' => 'media="print"', 'ie_conditional' => 'lte IE 6'));
// &lt;!--[if lte IE 6]&gt;
// &lt;link href="/assets/css/main.css" media="print" rel="stylesheet"/&gt;
// &lt;![endif]--&gt;
</pre>

<p class="important"><strong>Important</strong> - All path references in the css file (e.g. paths to background image files), should be changed to absolute if the script is printed <strong>inline</strong></p>



<a name="swf"></a>
<h2>$this->asset->swf(<var>'file'</var>, <var>'id'</var>, <var>'width'</var>, <var>'height'</var>, <var>[other_options]</var>)</h2>
<p>Will return code to embed a Flash swf file.
The first value is the name or the flash file.
The second parameter is html id value that swfobject should replace.
The third and fourth parameter is the width and height of the flash file respectively.
The fourth parameter is a catch all for additional parameter that can be passed which include:
</p>
<ul>
	<li><strong>vars</strong> - FlashVar variables to pass to the swf file</li>
	<li><strong>version</strong> - the Flash Player version to detect for. Default is Flash Player 9</li>
	<li><strong>color</strong> - the background color to be used. May be seen briefly before the swf file runs.</li>
	<li><strong>params</strong> - additional parameters to be passed to the swf file. For information on the additional parameters, visit the <a href="http://http://code.google.com/p/swfobject/wiki/documentation">swfobject documentation</a></li>
</ul>

<pre class="brush: php">
echo swf('home', 'home_flash', 800, 300, array('color' => '#000000', 'version' => 9));

// &lt;script src="/assets/js/swfobject.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;
// &lt;script type="text/javascript"&gt;
// //&lt;![CDATA[
//     var so = new SWFObject("/assets/swf/home.swf", "flash_swf", "800", "300", "9", "#000000");
//     so.write("flash");
// // ]]&gt;
// &lt;/script&gt;

</pre>

<p class="important"><strong>Important</strong> - Requires the <kbd>swfobject.js</kbd> to be located in the javascript assets folder. The swfoject being used is an older version (1.5)</p>

