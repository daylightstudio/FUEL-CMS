<h1>MY_Image_lib Class</h1>
<p>The <dfn>MY_Image_lib Class</dfn> extends <a href="http://codeigniter.com/user_guide/libraries/image_lib.html" target="_blank">CI_Image_lib</a> result class.
It adds a couple methods listed below:
</p>

<h2>$this->image_lib->resize_and_crop()</h2>
<p>
</p>

<pre class="brush:php">
$config['source_image']	= '/path/to/image/mypic.jpg';
$config['width']	 = 75;
$config['height']	= 50;

$this->load->library('image_lib', $config); 

$this->image_lib->resize_and_crop();
</pre>


<h2>$this->image_lib->convert()</h2>
<p>Converts an image to a different format. Borrowed from the <a href="http://codeigniter.com/forums/viewthread/145527/" target="_blank">CodeIgniter Forum.</a></p>

<pre class="brush:php">
$config['source_image']	= '/path/to/image/mypic.gif';

$this->load->library('image_lib', $config); 

$this->image_lib->convert('jpg');
</pre>
