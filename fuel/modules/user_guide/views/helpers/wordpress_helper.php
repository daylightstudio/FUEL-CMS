<h1>Wordpress Helper</h1>

<p>This helper is bridge between wordpress files and Codeigniter. It allows the user to inject code and output from the Codeigniter base into the wordpress schema.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('wordpress');
</pre>

<p>The following functions are available:</p>

<h2>wp_ci_initiliaze(<var>'ci_index'</var>, <var>'controller_method'</var>, <var>'echo'</var>)</h2>
<p>This function allows the user to pass the Codeigniter index.php file along with the controller/method to return/echo the result.  This would be primary used in a Wordpress page file where a rendered view from Codeigniter would be needed.</p>
<pre class="brush: php">
$path_to_index = '/var/www/html/index.php';
$uri = 'controller/method';
$echo = FALSE;

$content = wp_ci_initiliaze($path_to_index, $uri, $echo);
</pre>

<h2>is_wp()</h2>
<p>This function returns a boolean value if the Wordpress Codeigniter instance has been initialized. Helpful for when you are using a CodeIgniter view file and need to know if you are operating inside Wordpress.</p>
<pre class="brush: php">
if(is_wp() == TRUE)
{
    ...code goes here
}
</pre>

<h2>wp_ci_load_config(<var>'file'</var>)</h2>
<p>This function allows the user to import config variables from the Codeigniter namespace into a Wordpress namespace.  Useful when default configuration values are needed on a Wordpress blog page.</p>
<pre class="brush: php">
$config_file = 'config';
$config_array = wp_ci_load_config($config_file);
echo $config_array['some_var'];
</pre>

<h2>wp_ci_config_item(<var>'item'</var>)</h2>
<p>This function returns a particular config 'item' from the Codeigniter config file.  This would primarily be used when a single value only is needed from Codeigniter within a Wordpress blog page.</p>
<pre class="brush: php">
$config_var = 'some_var';
$config_item = wp_ci_config_item($config_var);
echo $config_item;
</pre>

<h2>wp_ci_load_view(<var>'view'</var>, <var>'vars'</var>, <var>'return'</var>)</h2>
<p>This function loads a view from the Codeigniter namespace into a Wordpress blog page such as a header or footer file. This function will automatically convert the <dfn>site_url</dfn> function into <dfn>wp_safe_site_url</dfn>.</p>
<pre class="brush: php">
$view_file = 'some_view_file';
$view_vars = array('key1'=>'value1', 'key2'=>'value2');
$return = TRUE;
$content = wp_ci_load_view($view_file, $view_vars, $return);
</pre>

<h2>wp_ci_load_helper(<var>'helper'</var>)</h2>
<p>This function loads a helper file from the Codeigniter namespace into the Wordpress namespace making its functions available for use.</p>
<pre class="brush: php">
$helper_file = 'some_helper_file';
wp_ci_load_helper($helper_file);
echo some_helper_function();
</pre>

<h2>wp_ci_load_library(<var>'library'</var>)</h2>
<p>This function loads a library file from the Codeigniter namespace into the Wordpress namespace making the object available for use.</p>
<pre class="brush: php">
$library_file = 'some_library_file';
wp_ci_load_library($library_file);
$library_object = new LibraryObject();
</pre>

<h2>site_url_wp_safe(<var>'uri'</var>)</h2>
<p>Wordpress has it's own <dfn>site_url</dfn> function which will cause a conflict. This function produces a fully qualified url based upon the uri passed as a parameter.</p>
<pre class="brush: php">
$uri = 'path/to/page';
$full_url = site_url_wp_safe($uri);
echo $full_uri; // http://mysite.com/path/to/page
</pre>
