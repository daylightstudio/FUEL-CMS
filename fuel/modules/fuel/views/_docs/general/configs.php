<h1>Config Files</h1>

<h2>FUEL Specific Application Config Files</h2>
<ul>
	<li><strong>asset:</strong> configuration parameters for the asset class</li>
	<li><strong>form_builder:</strong> used by FUEL's <a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder class</a> to set initialization preferences, 
				in particular custom field types. By default includes configuration parameters from the <span class="file">fuel/modules/fuel/config/form_builder.php</span> file</li>
	<li><strong>google:</strong> used by FUEL's <a href="<?=user_guide_url('helpers/google_helper')?>">Google helper</a></li>
	<li><strong>model:</strong> used by FUEL's <a href="<?=user_guide_url('libraries/my_model')?>">MY_Model class</a></li>
	<li><strong>MY_config:</strong> this configuration file is meant to augment the CodeIgniter config and is autoloaded. You can set additional <dfn>dev_mode</dfn>, <dfn>dev_email</dfn> (default testing email address) and <dfn>date_format</dfn> configuration parameters</li>
	<li><strong>MY_fuel_layouts:</strong> used to create the FUEL <a href="<?=user_guide_url('general/layouts')?>">layouts for the CMS</a></li>
	<li><strong>MY_fuel_modules:</strong> used to create FUEL <a href="<?=user_guide_url('modules/simple')?>">simple modules</a></li>
	<li><strong>MY_fuel:</strong> used to overwrite any of the default <a href="<?=user_guide_url('installation/configuration')?>">FUEL configuration settings</a></li>
	<li><strong>parser:</strong> used by the <a href="<?=user_guide_url('general/template-parsing')?>">template parsing engine</a> and is where you can specify which functions are allowed to be parsed</li>
	<li><strong>redirects:</strong> used to create <a href="<?=user_guide_url('general/redirects')?>">redirects</a> for FUEL</li>
	<li><strong>states:</strong> a configuration containing states. Can be used by forms mostly and in particular the custom field type <a href="<?=user_guide_url('general/forms#state')?>">states</a></li>
</ul>

<h2>CodeIgniter Specific Application Config Files</h2>
<ul>
	<li><strong>autoload:</strong> sets the packages, libraries, helpers, configs, languages and model files. 
		FUEL automatically includes the <span class="file">fuel/modules/fuel</span> folder as a package path which allows 
		it to store a lot of the files in that folder instead of the <span class="file">fuel/application</span> folder</li>
	<li><strong>config:</strong> the <a href="http://codeigniter.com/user_guide/libraries/config.html" target="_blank">CodeIgniter config file</a></li>
	<li><strong>constants:</strong> includes both the Codeigniter and FUEL specific constants and is loaded very early on in the bootstrap process</li>
	<li><strong>database:</strong> the <a href="http://codeigniter.com/user_guide/database/configuration.html" target="_blank">CodeIgniter database config file</a></li>
	<li><strong>doctypes:</strong> used in the <a href="http://codeigniter.com/user_guide/helpers/html_helper.html#doctype" target="_blank">CodeIgniter HTML helper function <dfn>doctype()</dfn></a></li>
	<li><strong>foreign_chars:</strong>  used in the <a href="http://codeigniter.com/user_guide/helpers/text_helper.html" target="_blank">CodeIgniter text helper function <dfn>convert_accented_characters()</dfn></a></li>
	<li><strong>hooks:</strong> the <a href="http://codeigniter.com/user_guide/general/hooks.html" target="_blank">CodeIgniter Hooks</a> configuration. By default it includes the <span class="file">fuel/modules/fuel/config/fuel_hooks.php</span>
		file which contains FUEL specific hooks. </li>
	<li><strong>migration:</strong> the <a href="http://codeigniter.com/user_guide/libraries/migration.html" target="_blank">CodeIgniter Migration</a> configuration. </li>
	<li><strong>mimes:</strong> the <a href="http://codeigniter.com/user_guide/libraries/migration.html" target="_blank">CodeIgniter mimes</a> configuration used by such classes 
			as the CodeIgniter <a href="http://codeigniter.com/user_guide/libraries/output.html" target="_blank">Output class</a>, the <a href="http://codeigniter.com/user_guide/libraries/file_uploading.html" target="_blank">Upload class</a>, 
			the <a href="http://codeigniter.com/user_guide/helpers/file_helper.html" target="_blank">file helper</a> and the <a href="http://codeigniter.com/user_guide/helpers/download_helper.html" target="_blank">download helper</a>.</li>
	<li><strong>routes:</strong> the <a href="http://codeigniter.com/user_guide/general/routing.html" target="_blank">CodeIgniter Routing</a> configuration</li>
	<li><strong>smileys:</strong> the <a href="http://codeigniter.com/user_guide/helpers/smiley_helper.html" target="_blank">Smiley helper function</a> configuration</li>
</ul>