<h1>Configuring FUEL CMS</h1>
<p>The following are the main configuration options that you can set for FUEL. 
They can be overwritten in the <dfn>application/config/MY_fuel.ph</dfn>p file:</p>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Preference</th>
			<th>Default Value</th>
			<th>Options</th>
			<th>Description</th>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>General Settings</h4></td>
		</tr>
		<tr>
			<td><strong>site_name</strong></td>
			<td>MyWebsite</td>
			<td>None</td>
			<td>The name of the site to be displayed at the top. Also used to generate your session key.</td>
		</tr>
		<tr>
			<td><strong>fuel_path</strong></td>
			<td>fuel/</td>
			<td>None</td>
			<td>Path to the fuel admin from the web base directory.</td>
		</tr>
		<tr>
			<td><strong>fuel_mode</strong></td>
			<td>views</td>
			<td>cms, views, auto</td>
			<td>The option <dfn>cms</dfn> pulls views and variables from the database, 
				<dfn>views</dfn> mode pulls views from the views folder and variables from the _variables folder.
				The <dfn>auto</dfn> option will first check the database for a page and if it doesn't exist or is not published, 
				it will then check for a corresponding view file.
			</td>
		</tr>
		<tr>
			<td><strong>login_redirect</strong></td>
			<td>fuel/dashboard</td>
			<td>None</td>
			<td>The page to redirect to AFTER logging in.</td>
		</tr>
		<tr>
			<td><strong>logout_redirect</strong></td>
			<td>:last</td>
			<td>None</td>
			<td>the page to redirect to AFTER logging out. Use the special value <strong>:last</strong> to redirect to the last page you were on.</td>
		</tr>
		<tr>
			<td><strong>domain</strong></td>
			<td>$_SERVER['SERVER_NAME']</td>
			<td>None</td>
			<td>Used for system emails. Can be overwritten by MY_fuel.php</td>
		</tr>
		<tr>
			<td><strong>from_email</strong></td>
			<td>'admin@'.$config['domain']</td>
			<td>None</td>
			<td>Used for system emails.</td>
		</tr>
		<tr>
			<td><strong>fuel_path</strong></td>
			<td>fuel/</td>
			<td>None</td>
			<td>Path to the fuel admin from the web base directory.</td>
		</tr>
		<tr>
			<td><strong>allow_forgotten_password</strong></td>
			<td>TRUE</td>
			<td>boolean TRUE/FALSE</td>
			<td>Allow for login link to allow forgotten passwords.</td>
		</tr>
		<tr>
			<td><strong>max_number_archived</strong></td>
			<td>5</td>
			<td>None</td>
			<td>Maximum number module items to archive.</td>
		</tr>
		<tr>
			<td><strong>warn_if_modified</strong></td>
			<td>TRUE</td>
			<td>boolean TRUE/FALSE</td>
			<td>Warn if a form field has changed before leaving page.</td>
		</tr>
		<tr>
			<td><strong>max_recent_pages</strong></td>
			<td>5</td>
			<td>None</td>
			<td>Max number of recent pages to display.</td>
		</tr>
		<tr>
			<td><strong>saved_page_state_max</strong></td>
			<td>5</td>
			<td>None</td>
			<td>The maximum number of pages that page state will be saved before dumping the last one saved. This is used on the list pages in the admin to save sorting and filtering. Used to save on space needed for session.</td>
		</tr>
		<tr>
			<td><strong>fuel_cookie_path</strong></td>
			<td>/</td>
			<td>None</td>
			<td>Provide a cookie path... different from the CI config if you need it (default is same as CI config).</td>
		</tr>
		<tr>
			<td><strong>xtra_css</strong></td>
			<td>None</td>
			<td>None</td>
			<td>External css file for additional styles possibly needed for 3rd party integration and customizing. 
			must exist in the assets/css file and not the fuel/assets/css folder.</td>
		</tr>
		<tr>
			<td><strong>keyboard_shortcuts</strong></td>
			<td>
<pre>
array(
	'toggle_view' => 'Ctrl+Shift+m', 
	'save' => 'Ctrl+Shift+s', 
	'view' => 'Ctrl+Shift+p'
);
</pre>
			</td>
			<td>None</td>
			<td>Keyboard shortcuts for the FUEL admin.</td>
		</tr>
		<tr>
			<td><strong>dashboards</strong></td>
			<td>
<pre>
array('fuel', 'backup');
</pre>
			</td>
			<td>None</td>
			<td>Dashboard modules to include on FUEL dashboard page.</td>
		</tr>
		<tr>
			<td><strong>dashboard_rss</strong></td>
			<td>http://www.thedaylightstudio.com/the-whiteboard/categories/fuel-cms/feed/rss</td>
			<td>None</td>
			<td>Dashboard rss feed to pull news FUEL news items.</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Asset settings</h4></td>
		</tr>
		<tr>
			<td><strong>fuel_assets_path</strong></td>
			<td>fuel/modules/{module}/assets/</td>
			<td>None</td>
			<td>Paths specific to FUEL... relative to the WEB_ROOT.</td>
		</tr>
		<tr>
			<td><strong>assets_excluded_dirs</strong></td>
			<td>
<pre>
array('js', 'css', 'cache', 'swf', 'captchas')
</pre>
			</td>
			<td>None</td>
			<td>Excludes certain folders from being viewed.</td>
		</tr>
		<tr>
			<td><strong>assets_allow_subfolder_creation</strong></td>
			<td>TRUE</td>
			<td>boolean TRUE/FALSE</td>
			<td>Allow subfolders to be created in the assets folder if they don't exist'.</td>
		</tr>
		<tr>
			<td><strong>editable_asset_filetypes</strong></td>
			<td>
<pre>
array(
	'images' => 'jpg|jpeg|jpe|gif|png', 
	'pdf' => 'pdf', 
	'media' => 'jpg|jpeg|jpe|png|gif|mov|mp3|aiff'
);
</pre>
			</td>
			<td>None</td>
			<td>Specifies what filetype extensions can be included in the folders.</td>
		</tr>
		<tr>
			<td><strong>assets_upload_max_size</strong></td>
			<td>1000</td>
			<td>None</td>
			<td>Max upload files size for assets.</td>
		</tr>
		<tr>
			<td><strong>assets_upload_max_width</strong></td>
			<td>1024</td>
			<td>None</td>
			<td>Max width for asset images beign uploaded.</td>
		</tr>
		<tr>
			<td><strong>assets_upload_max_height</strong></td>
			<td>768</td>
			<td>None</td>
			<td>Max height for asset images beign uploaded.</td>
		</tr>
		<tr>
			<td><strong>fuel_javascript</strong></td>
			<td>
<pre>
array(
	'jquery/plugins/date',
	'jquery/plugins/jquery.datePicker',
	'jquery/plugins/jquery.fillin',
	'jquery/plugins/jquery.markitup.pack',
	'jquery/plugins/jquery.markitup.set',
	'jquery/plugins/jquery.easing',
	'jquery/plugins/jquery.bgiframe',
	'jquery/plugins/jquery.tooltip',
	'jquery/plugins/jquery.scrollTo-min',
	'jquery/plugins/jqModal',
	'jquery/plugins/jquery.checksave',
	'jquery/plugins/jquery.form',
	'jquery/plugins/jquery.treeview.min',
	'jquery/plugins/jquery.hotkeys',
	'jquery/plugins/jquery.cookie',
	'jquery/plugins/jquery.fillin',
	'jquery/plugins/jquery.selso',
	'jquery/plugins/jquery-ui-1.8.4.custom.min',
	'jquery/plugins/jquery.disable.text.select.pack',
	'jquery/plugins/jquery.supercomboselect',
	'jquery/plugins/jquery.MultiFile.pack'
);
</pre>
			</td>
			<td>None</td>
			<td>Javascript files (mostly jquery plugins) to be included other then the controller js files.</td>
		</tr>
		<tr>
			<td><strong>fuel_css</strong></td>
			<td>None</td>
			<td>None</td>
			<td>CSS other then the fuel.css file which automatically gets loaded.</td>
		</tr>
		<tr>
			<td><strong>fuel_assets_output</strong></td>
			<td>None</td>
			<td>TRUE, FALSE, inline, gzip, whitespace, or combine</td>
			<td>Allow for asset optimization. Requires that all module folders have a writable assets/cache folder.</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Security settings </h4></td>
		</tr>
		<tr>
			<td><strong>default_pwd</strong></td>
			<td>admin</td>
			<td>None</td>
			<td>Default password to alert against.</td>
		</tr>
		<tr>
			<td><strong>admin_enabled</strong></td>
			<td>FALSE</td>
			<td>boolean TRUE/FALSE</td>
			<td>Enable the FUEL admin or not?</td>
		</tr>
		<tr>
			<td><strong>num_logins_before_lock</strong></td>
			<td>3</td>
			<td>None</td>
			<td>The number of times someone can attempt to login before they are locked out for 1 minute.</td>
		</tr>
		<tr>
			<td><strong>seconds_to_unlock</strong></td>
			<td>60</td>
			<td>None</td>
			<td>The number of seconds to lock out a person upon reaching the max number failed login attempts.</td>
		</tr>
		<tr>
			<td><strong>dev_password</strong></td>
			<td>None</td>
			<td>None</td>
			<td>If you set a dev password, the site will require a password to view.</td>
		</tr>
		<tr>
			<td><strong>auto_search_views</strong></td>
			<td>FALSE</td>
			<td>boolean TRUE/FALSE</td>
			<td>If the URI is about/history and the about/history view does not exist but about does, it will render the about page.</td>
		</tr>
		<tr>
			<td><strong>module_sanitize_funcs</strong></td>
			<td>TRUE</td>
			<td>boolean TRUE/FALSE, string of "xss", "php", "template" OR "entities", OR array for multiple values of array('xss', 'php', 'template', 'entities')</td>
			<td>Functions that can be used for the sanitize_input value on a basic module. The key of the array is what should be used when configuring your module</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Module Settings</h4></td>
		</tr>
		<tr>
			<td><strong>modules_allowed</strong></td>
			<td>
<pre>
array('blog', 'tools')
</pre>
			</td>
			<td>None</td>
			<td>Specifies which modules are allowed to be used in the fuel admin.</td>
		</tr>
		<tr>
			<td><strong>nav</strong></td>
			<td>
<pre>

// site... Dashboard will always be there
$config['nav']['site'] = array(
	'dashboard' => lang('module_dashboard'),
	'pages' => lang('module_pages'),
	'blocks' => lang('module_blocks'),
	'navigation' => lang('module_navigation'),
	'assets' => lang('module_assets'),
	'sitevariables' => lang('module_sitevariables')
	);

// if set to auto, then it will automatically include all in MY_fuel_modules.php
$config['nav']['shop'] = array();

// blog placeholder if it exists
$config['nav']['blog'] = array();

// if set to auto, then it will automatically include all in MY_fuel_modules.php
$config['nav']['modules'] = 'AUTO';

// tools
$config['nav']['tools'] = array();

// manage
$config['nav']['manage'] = array(
	'users' => lang('nav_users'), 
	'permissions' => lang('nav_permissions'),
	'manage/cache' => lang('nav_manage/cache'), 
	'manage/activity' => lang('nav_manage/activity')
	);
</pre>
			</td>
			<td>None</td>
			<td>Left navigation menu. Sub key menu items include <dfn>site</dfn>, <dfn>shop</dfn>, <dfn>blog</dfn>, <dfn>modules</dfn>, <dfn>tools</dfn> and <dfn>manage</dfn></td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Fuel Router Settings</h4></td>
		</tr>
		<tr>
			<td><strong>default_home_view</strong></td>
			<td>home</td>
			<td>None</td>
			<td>The default view for home.</td>
		</tr>
		<tr>
			<td><strong>use_page_cache</strong></td>
			<td>'cms'</td>
			<td>TRUE, FALSE 'cms', 'views'</td>
			<td>Turns on cache.</td>
		</tr>
		<tr>
			<td><strong>page_cache_ttl</strong></td>
			<td>0</td>
			<td>None</td>
			<td>How long to cache the page. A value of 0 means forever until the page or other modules have been updated.</td>
		</tr>
		<tr>
			<td><strong>page_cache_group</strong></td>
			<td>pages</td>
			<td>None</td>
			<td>The name of the group the cache is associated with (so you can just remove the group).</td>
		</tr>
		<tr>
			<td><strong>max_page_params</strong></td>
			<td>0</td>
			<td>None</td>
			<td>Maximum number of parameters that can be passed to the page. Used to cut down on queries to the db.</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>DB Table Settings</h4></td>
		</tr>
		<tr>
			<td><strong>tables</strong></td>
			<td>
<pre>
array(
	'archives' => 'fuel_archives',
	'logs' => 'fuel_logs',
	'navigation' => 'fuel_navigation',
	'navigation_groups' => 'fuel_navigation_groups',
	'pagevars' => 'fuel_page_variables',
	'pages' => 'fuel_pages',
	'blocks' => 'fuel_blocks',
	'permissions' => 'fuel_permissions',
	'user_to_permissions' => 'fuel_user_to_permissions',
	'users' => 'fuel_users'
);
</pre>
			</td>
			<td>None</td>
			<td>The FUEL specific database tables.</td>
		</tr>
		<tr>
			<td colspan="4" class="hdr"><h4>Page Settings</h4></td>
		</tr>
		<tr>
			<td><strong>auto_page_navigation_group_id</strong></td>
			<td>1</td>
			<td>None</td>
			<td>The group to associate with the auto-created navigation item.</td>
		</tr>
		<tr>
			<td><strong>page_uri_prefix</strong></td>
			<td>None</td>
			<td>None</td>
			<td>Automatically removes the following path from the location.</td>
		</tr>
	</tbody>
</table>