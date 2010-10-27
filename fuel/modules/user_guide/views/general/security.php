<h1>FUEL Security</h1>
<p>FUEL CMS has several security options you can change and they are listed below. In addition, cookie information is encrypted by default and you can specify your own encryption key in the configuration settings like any other
CI application.</p>

<h2>FUEL Configuration Security Settings</h2>
<p>The <a href="<?=user_guide_url('general/configuration')?>">FUEL config</a> provides several security settings to:</p>
<ul>
	<li><strong>restrict_to_remote_ip</strong> - restrict fuel to only certain ip addresses (array only value so can include multiple).</li>
	<li><strong>default_pwd</strong> - default password to alert against. The default password is <dfn>admin</dfn>.</li>
	<li><strong>admin_enabled</strong> - default password to alert against. The default is <dfn>TRUE</dfn>.</li>
	<li><strong>num_logins_before_lock</strong> - the number of times someone can attempt to login before they are locked out for 1 minute. The default is 3.</li>
	<li><strong>seconds_to_unlock</strong> - the number of seconds to lock out a person upon reaching the max number failed login attempts. The default is <dfn>60</dfn>.</li>
	<li><strong>dev_password</strong> - if you set a dev password, the site will require a password to view.</li>
</ul>

<h2>Module Specific Security Settings</h2>
<p>Additionally, the following module specific security settings exist:</p>
<ul>
	<li><strong>sanitize_input</strong> - cleans the input before inserting or updating the data source. Values can be either 
		<dfn>TRUE</dfn>, in which the <a href="http://codeigniter.com/user_guide/helpers/security_helper.html" target="_blank">xss_clean</a> function will be applied, 
		<dfn>php</dfn>, in which the <a href="http://codeigniter.com/user_guide/helpers/security_helper.html" target="_blank">encode_php_tags</a> function will be applied, or
		<dfn>FALSE</dfn>, in which no sanitizing will be used.</li>
	<li><strong>sanitize_images</strong> - uses <a href="http://codeigniter.com/user_guide/helpers/security_helper.html" target="_blank">xss_clean</a> function on images.</li>
</ul>

