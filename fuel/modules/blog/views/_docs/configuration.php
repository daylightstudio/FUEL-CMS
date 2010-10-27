<h1>Other Configurations</h1>
<p>The blog config file has additional configuration parameters to control the blog. You can </p>
<ul>
	<li><strong>blog_use_db_table_settings</strong> - determines whether to use the blog settings  or the configuration file  to configure the blog</li>
	<li><strong>blog_cache_group</strong> - the cache folder to hold blog cache files</li>
	<li>You can specify the same settings found in the <a href="<?=user_guide_url('modules/blog/settings')?>">Settings module</a> to control the blog if the above
	<dfn>blog_use_db_table_settings</dfn> is set to <dfn>FALSE</dfn>:
		<ul>
			<li><strong>title</strong></li>
			<li><strong>akismet_api_key</strong></li>
			<li><strong>uri</strong></li>
			<li><strong>theme_path</strong></li>
			<li><strong>use_cache</strong></li>
			<li><strong>cache_ttl</strong></li>
			<li><strong>per_page</strong></li>
			<li><strong>description</strong></li>
			<li><strong>use_captchas</strong></li>
			<li><strong>monitor_comments</strong></li>
			<li><strong>theme_layout</strong></li>
			<li><strong>save_spam</strong></li>
			<li><strong>allow_comments</strong></li>
			<li><strong>comments_time_limit</strong></li>
			<li><strong>theme_module</strong></li>
			<li><strong>multiple_comment_submission_time_limit</strong></li>
			<li><strong>asset_upload_path</strong></li>
		</ul>
	</li>
</ul>

<h2>Programmer specific</h2>
<p>The config file also contains several programmer specific configurations:</p>
<ul>
	<li><strong>formatting</strong> - the formatting options allowed for blog posts. Options are Automatic (which uses CodeIgniter's <a href="http://codeigniter.com/user_guide/helpers/typography_helper.html" target="_blank">auto_typography</a> function), <a href="<?=user_guide_url('helpers/markdown')?>">Markdown</a>  or None</li>
	<li><strong>captcha</strong> - contains an array of configuration to configure captchas (if they are enabled in the settings)
		<ul>
			<li><strong>img_width</strong> - 120</li>
			<li><strong>img_height</strong> - 26</li>
			<li><strong>expiration</strong> - 600 (10 minutes)</li>
			<li><strong>bg_color</strong> => #4b4b4b</li>
			<li><strong>char_color</strong> => #ffffff,#cccccc,#ffffff,#999999,#ffffff,#cccccc</li>
			<li><strong>line_color</strong> => #ff9900,#414141,#ea631d,#aaaaaa,#f0a049,#ff9900</li>
		</ul>
	</li>
	<li><strong>comment_form</strong> - configuration parameters used for the <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder Class</a> which is used for creating the form</li>
</ul>