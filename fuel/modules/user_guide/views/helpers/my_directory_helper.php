<h1>MY Directory Helper</h1>

<p>Contains functions to be used with directories. Extends CI's directory helper.</p>

<p>This helper is loaded using the following code:</p>

<pre class="brush: php">
$this->load->helper('directory');
</pre>

<p>The following functions are available:</p>

<h2>copyr(<var>source_dir</var>, <var>dest</var>)</h2>
<p>Recursively copies from one directory to another.</p>

<h2>chmodr(<var>source_dir</var>, <var>permissions</var>)</h2>
<p>Recursively changes permissions on a directory structure.</p>


<h2>directory_to_array(<var>directory</var>, <var>[recursive]</var>, <var>[exclude]</var>, <var>[append_path]</var>, <var>[no_ext]</var>)</h2>
<p>Returns an array of file names from a directory.
<dfn>recursive</dfn> will recursively look through directories and the default is TRUE.
<dfn>exclude</dfn> excludes files from being listed. Can be either an array of file names or a regular expression string.
<dfn>append_path</dfn> appends the full path to the array key path value.
<dfn>no_ext</dfn> says whether to include the file extension in the return. Default is FALSE.
</p>

<h2>list_directories(<var>directory</var>, <var>[exclude]</var>, <var>[full_path]</var>, <var>[is_writable]</var>)</h2>
<p>Recursively lists directories from within a given directory.
<dfn>exclude</dfn> excludes files from being listed. Can be either an array of file names or a regular expression string.
<dfn>full_path</dfn> says whether to include the full path in the liste.
<dfn>is_writable</dfn> is whether to include just writable files. Default is FALSE.
</p>
