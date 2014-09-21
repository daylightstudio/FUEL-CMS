<h1>Environments</h1>
<p>The CodeIgniter framework allows you to set an <dfn>ENVIRONMENT</dfn> constant in your bootstrap index.php file. This constant can be used to pull in different configuration information.
	However, it can become a hassle to remember to change that value when you switch environments. FUEL has added the ability to set up an array of server host values that correspond to 
	different environments and will automatically set the <dfn>ENVIRONMENT</dfn> constant value. To set your environments, edit the <span class="file">/fuel/application/config/environments.php</span> file. 
	If there is no matching environment it is setup to default to the <dfn>development</dfn> environment. The default can be changed in the index.php bootstrap file.
</p>

<p>The <span class="file">/fuel/application/config/environments.php</span> file by default includes the following development environments:</p>
<pre class="brush:php">
$environments = array(
			'development' => array('localhost*', '192.*', '*.dev'),
			);
</pre>
<p class="important">You can use regular expression or asterisks as a wildcards.</p>