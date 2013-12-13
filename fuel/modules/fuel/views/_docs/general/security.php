<h1>FUEL Security</h1>
<p>FUEL CMS has several security options you can change and they are listed below. In addition, cookie information is encrypted by default and you can specify your own encryption key in the configuration settings like any other
CI application.</p>

<p class="important">FUEL CMS 1.0 improved the security hashing used for storing passwords in the database.</p>

<h2>FUEL Configuration Security Settings</h2>
<p>The <a href="<?=user_guide_url('general/configuration')?>">FUEL config</a> provides several security settings to:</p>
<ul>
	<li><strong>restrict_to_remote_ip</strong> - restrict FUEL to only certain IP addresses (array only value so can include multiple).</li>
	<li><strong>default_pwd</strong> - default password to alert against. The default password is <dfn>admin</dfn>.</li>
	<li><strong>admin_enabled</strong> - allow use of the CMS admin. The default is <dfn>FALSE</dfn>.</li>
	<li><strong>num_logins_before_lock</strong> - the number of times someone can attempt to login before they are locked out for 1 minute (or whatever is set for the <dfn>seconds_to_unlock</dfn>). The default is <dfn>3</dfn>.</li>
	<li><strong>seconds_to_unlock</strong> - the number of seconds to lock out a person upon reaching the max number failed login attempts. The default is <dfn>60</dfn>.</li>
	<li><strong>dev_password</strong> - if you set a dev password, the site will require a password to view. Testing your site may not work if a dev password is set. Default is no password.</li>
</ul>

<h2>Module Specific Security Settings</h2>
<p>Additionally, the following module specific security settings exist:</p>
<ul>
	<li><strong>sanitize_input</strong> - cleans the input before inserting or updating the data source. 
		A value of <dfn>TRUE</dfn>, will apply the <a href="http://codeigniter.com/user_guide/helpers/security_helper.html" target="_blank">xss_clean</a> function. 
		A value of <dfn>FALSE</dfn>, will apply no sanitation functions.
		You can use an array to appy more then one function to sanitize your input.
		The list of functions to sanitize the input is set by the <dfn>module_sanitize_funcs</dfn> <a href="<?=user_guide_url('general/configuration')?>">FUEL configuration</a> value under the security settings.
		The default values are listed below:
		<ul>
			<li><strong>xss</strong> = xss_clean</li>
			<li><strong>php</strong> = encode_php_tags</li>
			<li><strong>template</strong> = php_to_template_syntax</li>
			<li><strong>entities</strong> = entities</li>
		</ul>
	<li><strong>sanitize_files</strong> (was sanitize_images) - uses <a href="http://codeigniter.com/user_guide/helpers/security_helper.html" target="_blank">xss_clean</a> function on images.</li>
</ul>

