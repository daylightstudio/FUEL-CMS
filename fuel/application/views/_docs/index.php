<h1>Site Documentation for <?=$this->config->item('site_name', 'fuel')?></h1>
<p>The following contains documentation regarding your FUEL CMS website. This site is running FUEL CMS <?=FUEL_VERSION?>. Additional FUEL documentation can be found at <a href="http://docs.getfuelcms.com" target="_blank">docs.getfuelcms.com</a>.</p>
<ul>
	<li><a href="#what">What is FUEL CMS?</a></li>
	<li><a href="#modules">Modules</a></li>
	<li><a href="#dashboard">Dashboard</a></li>
	<li><a href="#pages">Pages</a></li>
	<li><a href="#blocks">Blocks</a></li>
	<li><a href="#navigation">Navigation</a></li>
	<li><a href="#tags">Tags</a></li>
	<li><a href="#categories">Categories</a></li>
	<li><a href="#assets">Assets</a></li>
	<li><a href="#sitevariables">Site Variables</a></li>
	<li><a href="#users">Users &amp; Permissions</a></li>
	<li><a href="#cache">Page Cache</a></li>
	<li><a href="#settings">Settings</a></li>
</ul>

<h2 id="what">What is FUEL CMS?</h2>
<p><a href="http://getfuelcms.com" target="_blank">FUEL CMS</a> is a hybrid of a CMS and a framework.
At it's core, FUEL is a PHP/MySQL, modular-based development platform built on top of the popular <a href="http://www.codeigniter.com" target="_blank">CodeIgniter</a> framework. </p>

<p>Learn more at <a href="http://getfuelcms.com" target="_blank">getfuelcms.com</a>.</p>

<h2 id="modules">Modules</h2>
<p>The <?=$this->fuel->config('site_name')?> website contains the following modules.</p>

<h3>Core Modules</h3>
<ul>
	<li><strong>Site</strong> - The following modules are part of the core functionality of FUEL CMS:
		<ul>
			<li><strong><a href="#dashboard">Dashboard</a></strong> - an area for modules to display immediate relevant content such as recently modified pages and a link to this page.</li>
			<li><strong><a href="#pages">Pages</a></strong> - create website pages. Pages created in the CMS will take precedence over any <a href="http://docs.getfuelcms.com/general/pages-variables" target="_blank">static pages</a>.</li>
			<li><strong><a href="#navigation">Navigation</a></strong> - create navigation items.</li>
			<li><strong><a href="#blocks">Blocks</a></strong> - create reusable block elements (e.g. headers, footers, callouts, etc).</li>
			<li><strong><a href="#categories">Categories</a></strong> - create categories to group records together.</li>
			<li><strong><a href="#tags">Tags</a></strong> - create tags to associate with one or more other module records to allow for easy filtering.</li>
			<li><strong><a href="sitevariables">Site Variables</a></strong> - create variables that can be used throughout your website (e.g. a contact email address).</li>
			<li><strong><a href="#users">Users</a></strong> - create users and associate permissions with them.</li>
			<li><strong><a href="#permissions">Permissions</a></strong> - create permissions to associate with other users.</li>
		</ul>
	</li>
	
	<li><strong>Manage</strong> - The following modules are used to manage various aspects of the site:
		<ul>
			<li><strong><a href="#users">Users</a></strong> - allows you to create and manage permissions of FUEL CMS users.</li>
			<li><strong><a href="#permissions">Permissions</a></strong> - used to manage permissions and associate to users.</li>
			<li><strong><a href="#cache">Page Cache</a></strong> - used to clear the cache of the site.</li>
			<li><strong>Activity Log</strong> - allows you to view the activity logs within FUEL CMS.</li>
			<li><strong><a href="#settings">Settings</a></strong> - used to manage module specific configurations.</li>
		</ul>
	</li>	
</ul>

<h3>Installed Modules</h3>
<p>The following modules are currently installed for your website:</p>
<?php $modules = $this->fuel->modules->advanced();?>
<ul>
<?php foreach($modules as $mod) :  ?>
	<li>
		<strong><?=$mod->friendly_name()?></strong>
		<?php if ($mod->install_info('description')) : ?> - <?=$mod->install_info('description')?> <?php endif; ?>
		<?php  
			$submodules = $mod->submodules();
			if (!empty($submodules)) : 
		?>
		<ul>
			<?php  foreach($submodules as $sub) : ?>
			<li><?=$sub->info('module_name')?></li>
			<?php endforeach;?>
		</ul>
		
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>


<h2 id="dashboard" class="ico ico_dashboard"><img src="<?=img_path('icons/ico_house.png', 'fuel')?>" alt="Dashboard" /> <a href="<?=fuel_url('dashboard')?>">Dashboard</a></h2>
<p>The FUEL CMS dashboard displays the latest activity within the system, FUEL news as well as a link to this documentation. </p>


<h2 id="pages" class="ico ico_pages"><img src="<?=img_path('icons/ico_layout.png', 'fuel')?>" alt="Pages" /> <a href="<?=fuel_url('pages')?>">Pages</a></h2>
<p>A page in FUEL CMS is a combination of assigning a <a href="#layouts">layout</a> and layout variables, to a URI location (e.g. company/contact).</p>
<p>More can be read about creating pages and variables in the <a href="http://docs.getfuelcms.com/general/pages-variables" target="_blank">FUEL CMS User Guide</a>.</p>

<h3 id="layouts"><img src="<?=img_path('icons/ico_layout.png', 'fuel')?>" alt="Layouts" /> Layouts</h3>
<p>Layouts are predefined HTML files located in the <dfn><?=APPPATH?>views/_layouts</dfn> folder and are used for placing content in your site pages.
	Each layout is associated with a set of fields that can be edited when creating a page which is found in the <dfn><?=APPPATH?>config/MY_fuel_layouts.php</dfn>.
	Layouts themselves are not editable in the CMS, however, there are some layouts (e.g. None), that allow 
	you to edit anything in the page. When creating/editing a page in the CMS, the following layouts are available:</p>

<ul>
	<?php $layouts = $this->fuel->layouts->get(); ?>
	<?php foreach($layouts as $layout) : ?>
	<li><strong><?=$layout->name()?></strong><?php if ($layout->description()) : ?> - <?=$layout->description()?><?php endif; ?></li>
	<?php endforeach; ?>
</ul>

<h3>Tabbed Sections</h3>
<p>Most layouts are broken up into tabs to make inputting content easier. Most layouts have at least one tab for <strong>Meta</strong> information which allows you to change the page title, meta description and
meta keywords for the page.</p>

<?php if ($this->fuel->config('text_editor') == 'markitup') : ?>
<h3>Inputting HTML Text</h3>
<p>For the fields that require large amounts of text, FUEL provides a textarea with controls that allow you to quickly insert HTML code. Mousing over the controls will
provide tooltips as to what each control will add to your content field.
 This method was chosen over using a true WYSIWYG (What-You-See-Is-What-You-Get) because it ensures that the code entered is what is applied in the final rendered view.
 Additionally, most of the layouts are setup to automatically format certain fields with paragraph tags (&lt;p&gt;) if they are ommitted. If an area requires a large amount of 
 editing space, you can click the full screen button <img src="<?=img_path('markitup/minimize.png', 'fuel')?>" alt="minimize" /> to expand the editing region. Lastly,
 the <strong>preview</strong> button will display the contents of the field within a separate window so you can see it rendered using the site's style sheet.
</p>
<?php endif; ?>

<h3>Special Functions</h3>
<p>When inputting content, you can use special template functions to help insert things such 
as page URLS and image paths. The most common are:</p>
<ul>
	<li><strong>{site_url('my_page')}</strong> - inserts a link path relative to the site (e.g. http://www.marchex.com/my_page)/z.</li>
	<li><strong>{img_path('my_image.jpg')}</strong> - inserts the image path based (e.g. /assets/images/my_image.jpg)/.</li>
	<li><strong>{pdf_path('my_pdf.pdf')}</strong> - inserts the pdf path based (e.g. /assets/pdf/my_pdf.pdf). Extension (.pdf) is optional.</li>
	<li><strong>{docs_path('my_doc.doc')}</strong> - inserts the pdf path based (e.g. /assets/pdfs/my_doc.doc).</li>
	<li><strong>{safe_mailto('info@mysite.com', 'Contact Us')}</strong> - creates a mailto link but uses javascript to help obufiscate it from email harvesting bots.</li>
</ul>
<p><a href="http://docs.getfuelcms.com/general/template-parsing" target="_blank">Click here for a more complete list.</a></p>

<h3>Inline Editing</h3>
<p>FUEL CMS allows you to edit the contents of a page within the context of your site. To do so, you must first be logged into the CMS, which if you are reading this, you already are.
Then you can browse your site and you will notice a small icon in the upper right corner that looks like <img src="<?=img_path('icons/ico_fuel.png', 'fuel')?>" alt="FUEL" />.
Clicking on <img src="<?=img_path('icons/ico_fuel.png', 'fuel')?>" alt="FUEL" /> (FUEL icon) will expand a contextual menu depending on the type of page. If inline editable areas are available on the page, you will see a <img src="<?=img_path('icons/ico_pencil.png', 'fuel')?>" alt="FUEL" />
icon in the menu. Clicking this will display pencil icons over editable regions. Clicking one of those icons will display the form fields associated with editing that particular region.
</p>



<h2 id="blocks" class="ico ico_blocks"><img src="<?=img_path('icons/ico_application_view_tile.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('blocks')?>">Blocks</a></h2>
<p>Blocks are reusable areas of your site like the header and footer. They can also contain dynamic logic (e.g. displaying the most recent news items). Blocks can be static and uneditable, or they can be managed in the CMS.</p>
<ul>
	<li><strong>Name</strong> - the navigation group to associate with.</li>
	<li><strong>Description</strong> - the URI path or absolute URL to a page.</li>
	<li><strong>View</strong> - a unique identifier for the page used for building parent child hierarchies and by default is the same as the "Location" value.</li>
	<li><strong>Published</strong> - whether to display or hide the block item.</li>
</ul>

<h2 id="navigation" class="ico ico_navigation"><img src="<?=img_path('icons/ico_sitemap_color.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('navigation')?>">Navigation</a></h2>
<p>The header and footer menu is controlled by the Navigation module. When creating or editing a navigation item, you can set the following fields:</p>
<ul>
	<li><strong>Group</strong> - the navigation group to associate with which can be the <dfn>main</dfn> or <dfn>footernav</dfn> group.</li>
	<li><strong>Location</strong> - the URI path or absolute URL to a page.</li>
	<li><strong>Nav key</strong> - a unique identifier for the page used for building parent child hierarchies and by default is the same as the "Location" value.</li>
	<li><strong>Label</strong> - the text displayed with the menu item.</li>
	<li><strong>Parent</strong> - the parent menu item to associate with the item and help build the hierarchy.</li>
	<li><strong>Precedence</strong> - the ordering of the menu item related to it's parent.</li>
	<li><strong>Attributes</strong> - link attributes (e.g. target="_blank") to associate with the menu item.</li>
	<li><strong>Selected</strong> - helps determine whether the menu item should be shown in the selected state based on the current page.</li>
	<li><strong>Hidden</strong> - an attribute that can be used on the front end for displaying certain menu items.</li>
	<li><strong>Published</strong> - whether to display or hide the menu item.</li>
</ul>


<h2 id="tags" class="ico ico_tags"><img src="<?=img_path('icons/ico_tag_blue.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('tags')?>">Tags</a></h2>
<p>The Tags module is a generic way to assign many-to-many relationships between things. When creating or editing a tag, you can set the following fields:</p>
<ul>
	<li><strong>Name</strong> - the name of the tag.</li>
	<li><strong>Category</strong> - the <a href="#categories">category</a> in which the tag belongs if any (e.g. Bags, Straps, Accessories). </li>
	<li><strong>Slug</strong> - the URI identifier that can be used for filtering (not applicable).</li>
	<li><strong>Published</strong> - whether to display or hide the tag if used on a page.</li>
</ul>


<h2 id="categories" class="ico ico_categories"><img src="<?=img_path('icons/ico_folder_page.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('categories')?>">Categories</a></h2>
<p>The Categories module is used for grouping records together. When creating or editing a category, you can set the following fields:</p>
<ul>
	<li><strong>Name</strong> - the name of the category.</li>
	<li><strong>Slug</strong> - the URI identifier that can be used for filtering (not applicable).</li>
	<li><strong>Context</strong> - a context in which the category belongs to allow for further filtering of categories.</li>
	<li><strong>Precedence</strong> - the ordering value which can be used for display purposes (e.g. the lower the value the higher on the list).</li>
	<li><strong>Parent</strong> - the parent category in which this category belongs to if any (not applicable).</li>
	<li><strong>Published</strong> - whether to display or hide the category if used on a page.</li>
</ul>

<h2 id="assets" class="ico ico_assets"><img src="<?=img_path('icons/ico_pictures.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('assets')?>">Assets</a></h2>
<p>The Assets module is used to manage assets for your site like images and PDFs. When uploading a new asset, you can set the following:</p>
<ul>
	<li><strong>File</strong> - the asset file to upload (.png, jpeg, PDF, etc).</li>
	<li><strong>Asset folder</strong>  - the folder to upload the file to. By default the "images" folder can be used for all images and the "pdf" folder for any PDFs.</li>
	<li><strong>New file name</strong> - the new file name if you want it to be changed from it's current name.</li>
	<li><strong>Overwrite</strong> - determines whether to overwrite an existing file with the same name or create a new file with a number appended to the name to prevent overwriting.</li>
	<li><strong>Unzip zip files</strong> - determines whether to unzip any uploaded zip files.</li>
	<li><strong>Create thumb (Image Only)</strong> - determines whether to create a thumbnail based on the uploaded file. Setting the height and width values mentioned below will
		controller the thumbnail size if this box is selected.</li>
	<li><strong>Maintain ratio (Image Only)</strong> - determines whether to maintain the images aspect ratio if being resized.</li>
	<li><strong>Width (Image Only)</strong> - the width of the new image or thumbnail (if box is checked).</li>
	<li><strong>Height (Image Only)</strong> - the height of the new image or thumbnail (if box is checked).</li>
	<li><strong>Master dimension (Image Only)</strong> - determines which dimension to use if needing to mainain aspect ratio and a new width or height don't fit.</li>
</ul>

<h2 id="sitevariables" class="ico ico_sitevariables"><img src="<?=img_path('icons/ico_page_white_code.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('sitevariables')?>">Site Variables</a></h2>
<p>The Site Variables module is used for setting up site wide variables that can be accessible by multiple pages (e.g. Twitter handlers, etc). This module is currently not being used. 
	When creating or editing a site variable, you can set the following fields:</p>
<ul>
	<li><strong>Name</strong> - the variable name.</li>
	<li><strong>Value</strong> - the value of the variable.</li>
	<li><strong>Scope</strong> - the scope in which the variable can be applied. The scope value can be a regular expression (e.g. products/:any).</li>
	<li><strong>Active</strong> - whether to display or hide the site variable if used on a page.</li>
</ul>

<h2 id="users" class="ico ico_users"><img src="<?=img_path('icons/ico_key.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('users')?>">Users &amp; Permissions</a></h2>
<p>FUEL CMS users are created in the users module in the admin. A single user can subscribe to as many permissions as necessary however, the permissions to manage users and permisisons
gives a user admin level control so use wisely. Furthermore, certain permissions may not be applicable to your setup.</p>

<h2 id="cache" class="ico ico_manage_cache"><img src="<?=img_path('icons/ico_page_lightning.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('manage/cache')?>">Page Cache</a></h2>
<p>FUEL CMS uses a cache to speed up the delivery of pages. Sometimes changes are made to static layouts or blocks and your changes may not be immediately reflected. This is most likely
related to the cache which can be <a href="<?=fuel_url('manage/cache')?>">cleared here</a>.</p>

<h2 id="settings" class="ico ico_settings"><img src="<?=img_path('icons/ico_table_gear.png', 'fuel')?>" alt="Layouts" /> <a href="<?=fuel_url('settings')?>">Settings</a></h2>
<p>Although it's unlikely you'll need to worry too much about this, some modules have extra configuration settings you can manage in the CMS. For example, you may have a blog settings area if the blog is installed.</p>

