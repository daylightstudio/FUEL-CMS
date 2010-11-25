<h1>MY File Helper</h1>

<p>Contains functions to be used with files. Extends CI's file helper.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('file');
</pre>

<p>The following functions are available:</p>

<h2>get_dir_file_info(<var>source_dir</var>, <var>[include_path]</var>)</h2>
<p>Overwrites CI's <a href="http://codeigniter.com/user_guide/helpers/file_helper.html">get_dir_file_info</a> and adds the ability to include the path in the array returned which defaults to FALSE.</p>

<h2>delete_files(<var>path</var>, <var>[del_dir]</var>, <var>[exclude]</var>, <var>[level]</var>)</h2>
<p>Overwrites CI's <a href="http://codeigniter.com/user_guide/helpers/file_helper.html">delete_files</a> and adds the ability to exclude certain files by passing
either an array of file names or a regular expression string.
<dfn>del_dir</dfn> defaults to <dfn>FALSE</dfn>.
<dfn>exclude</dfn> excludes files from being deleted. Can be either an array of file names or a regular expression string.
<dfn>level</dfn> how many levels deep to delete.
</p>

<h2>is_image_file(<var>path</var>)</h2>
<p>Determines if the path value is an image. Returns a boolean.</p>
