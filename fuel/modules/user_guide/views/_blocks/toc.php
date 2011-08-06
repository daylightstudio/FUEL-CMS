<table border="0" cellspacing="0" cellpadding="0" class="toc_table">
	<tbody>
		<tr>
			<td class="td">
				
				<?php if ($site_docs) : ?>
				<h2>Site Reference</h2>
				<ul>
					<li><a href="<?=user_guide_url('site')?>">Site Documentation</a></li>
				</ul>
				<?php endif; ?>

				
				
				<h2>Basic Info</h2>
				<ul>
					<li><a href="<?=user_guide_url('general/requirements')?>">Server Requirements</a></li>
					<li><a href="<?=user_guide_url('general/license')?>">License Agreement</a></li>
					<li><a href="<?=user_guide_url('general/credits')?>">Credits</a></li>
					<li><a href="<?=user_guide_url('general/contribute')?>">Contribute</a></li>
				</ul>

				<h3>Getting Started</h3>
				<ul>
					<li><a href="<?=user_guide_url('general/what-is-fuel')?>">What is FUEL CMS?</a></li>
					<li><a href="<?=user_guide_url('general/installing')?>">Installing FUEL CMS</a></li>
					<li><a href="<?=user_guide_url('general/configuration')?>">Configuring FUEL CMS</a></li>
					<li><a href="<?=user_guide_url('general/quickstart')?>">Quick Start</a></li>
					<li><a href="<?=user_guide_url('general/creating-pages')?>">Creating Pages</a></li>
					<li><a href="<?=user_guide_url('general/pages-layouts-modules-blocks')?>">Pages, Layouts, Modules &amp; Blocks</a></li>
				</ul>
				
				<h3>General Topics</h3>
				<ul>
					<li><a href="<?=user_guide_url('general/interface')?>">FUEL CMS Interface</a></li>
					<li><a href="<?=user_guide_url('general/opt-in-controllers')?>">Opt-in Controller Development</a></li>
					<li><a href="<?=user_guide_url('general/inline-editing')?>">Inline Editing</a></li>
					<li><a href="<?=user_guide_url('general/security')?>">Security</a></li>
					<li><a href="<?=user_guide_url('general/localization')?>">Localization</a></li>
					<li><a href="<?=user_guide_url('general/redirects')?>">Redirects</a></li>
					<li><a href="http://www.getfuelcms.com/blog/2011/03/14/fuel-cms-0.9.3-released">What's New With 0.9.3</a></li>
				</ul>

				<h3>Additional Resources</h3>
				<ul>
					<li><a href="http://www.getfuelcms.com/blog">FUEL CMS's Blog</a></li>
					<li><a href="http://www.thedaylightstudio.com/the-whiteboard/categories/fuel-cms">Daylight's Blog</a></li>
					<li><a href="http://codeigniter.com">CodeIgniter Website</a></li>
				</ul>

			</td>
			<td class="td_sep">
					
				<h2>Modules</h2>
				<ul>
					<li><a href="<?=user_guide_url('modules')?>">Modules Overview</a></li>
					<li><a href="<?=user_guide_url('modules/simple')?>">Simple Modules</a></li>
					<li><a href="<?=user_guide_url('modules/advanced')?>">Advanced Modules</a></li>
					<li><a href="<?=user_guide_url('modules/tutorial')?>">Creating Modules</a></li>
					<li><a href="<?=user_guide_url('modules/forms')?>">Module Forms</a></li>
					<li><a href="<?=user_guide_url('modules/hooks')?>">Module Hooks</a></li>
				</ul>

				<?php if (!empty($modules)) : ?>
				<h2>Specific Module Reference</h2>
				<ul>
				<?php foreach($modules as $uri => $module) : ?>
					<li><a href="<?=user_guide_url('modules/'.$uri)?>"><?=$module?></a></li>
				<?php endforeach; ?>
				</ul>
				<?php endif; ?>
				
				<h2>View Parsing</h2>
				<ul>
					<li><a href="<?=user_guide_url('parsing')?>">Parsing Overview</a></li>
					<li><a href="<?=user_guide_url('parsing/parsing_examples')?>">Parsing Examples</a></li>
				</ul>

				<h2>Javascript</h2>
				<ul>
					<li><a href="<?=user_guide_url('javascript/jqx')?>">jQX Framework</a></li>
				</ul>
				
			</td>
			<td class="td_sep">
				<h2>Classes</h2>
				
				<h3>FUEL Classes Reference</h3>
				<ul>
					<li><a href="<?=user_guide_url('libraries/asset')?>">Asset Class</a></li>
					<li><a href="<?=user_guide_url('libraries/data_table')?>">Data_table Class</a></li>
					<li><a href="<?=user_guide_url('libraries/form')?>">Form Class</a></li>
					<li><a href="<?=user_guide_url('libraries/form_builder')?>">Form_builder Class</a></li>
					<li><a href="<?=user_guide_url('libraries/menu')?>">Menu Class</a></li>
					<li><a href="<?=user_guide_url('libraries/validator')?>">Validator Class</a></li>
				</ul>

				<h3>Abstract FUEL Classes Reference</h3>
				<ul>
					<li><a href="<?=user_guide_url('libraries/my_model')?>">MY_Model Class</a></li>
					<li><a href="<?=user_guide_url('libraries/base_module_model')?>">Base_module_model Class</a></li>
				</ul>
				
				<h3>3rd Party Classes</h3>
				<ul>
					<li><a href="<?=user_guide_url('libraries/modular_extensions')?>">Modular Extensions - HMVC Class</a></li>
					<li><a href="<?=user_guide_url('libraries/cache')?>">Cache Class</a></li>
					<li><a href="<?=user_guide_url('libraries/template')?>">Template Class</a></li>
					<li><a href="<?=user_guide_url('libraries/simplepie')?>">Simplepie Class</a></li>
				</ul>
				
				<h3>Extended Base Classes</h3>
				<ul>
					<li><a href="<?=user_guide_url('libraries/my_db_mysql_driver')?>">MY_DB_mysql_driver Class</a></li>
					<li><a href="<?=user_guide_url('libraries/my_db_mysql_result')?>">MY_DB_mysql_result Class</a></li>
					<li><a href="<?=user_guide_url('libraries/my_model')?>">MY_Model Class</a></li>
					<li><a href="<?=user_guide_url('libraries/my_image_lib')?>">MY_Image_lib Class</a></li>
					<li><a href="<?=user_guide_url('libraries/my_parser')?>">MY_Parser Class</a></li>
					<li><a href="<?=user_guide_url('libraries/my_uri')?>">MY_URI Class</a></li>
				</ul>

			</td>
			<td class="td_sep">
				<h2>Helpers</h2>
				<ul>
					<li><a href="<?=user_guide_url('helpers/my_helper')?>">MY helper</a></li>
				</ul>
				<h3>FUEL Helpers</h3>
				<ul>
					<li><a href="<?=user_guide_url('helpers/ajax_helper')?>">Ajax helper</a></li>
					<li><a href="<?=user_guide_url('helpers/asset_helper')?>">Asset helper</a></li>
					<li><a href="<?=user_guide_url('helpers/browser_helper')?>">Browser helper</a></li>
					<li><a href="<?=user_guide_url('helpers/compatibility_helper')?>">Compatibility helper</a></li>
					<li><a href="<?=user_guide_url('helpers/convert_helper')?>">Convert helper</a></li>
					<li><a href="<?=user_guide_url('helpers/fuel_helper')?>">FUEL helper</a></li>
					<li><a href="<?=user_guide_url('helpers/format_helper')?>">Format helper</a></li>
					<li><a href="<?=user_guide_url('helpers/google_helper')?>">Google helper</a></li>
					<li><a href="<?=user_guide_url('helpers/utility_helper')?>">Utility helper</a></li>
					<li><a href="<?=user_guide_url('helpers/validator_helper')?>">Validator helper</a></li>
				</ul>
				
				<h3>3rd Party Helpers</h3>
				<ul>
					<li><a href="<?=user_guide_url('helpers/markdown_helper')?>">Markdown helper</a></li>
				</ul>
				
				<h3>Extended Helpers</h3>
				<ul>
					<li><a href="<?=user_guide_url('helpers/my_array_helper')?>">MY_array helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_date_helper')?>">MY_date helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_directory_helper')?>">MY_directory helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_file_helper')?>">MY_file helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_html_helper')?>">MY_html helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_language_helper')?>">MY_language helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_string_helper')?>">MY_string helper</a></li>
					<li><a href="<?=user_guide_url('helpers/my_url_helper')?>">MY_url helper</a></li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>