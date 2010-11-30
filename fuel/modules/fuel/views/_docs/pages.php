<h1>Pages</h1>
<p>The Fuel Pages module allows you to create pages for your website based on a <a href="<?=user_guide_url('modules/fuel/layouts')?>">layout</a>.

</p>

<h2>Module List View</h2>
<p>There is a standard list view and a tree view of the pages you can edit within FUEL.</p>
<img src="<?=img_path('examples/screen_pages_list.jpg', 'user_guide')?>" class="screen" />


<h2>Module Create/Edit View</h2>
<p>To create a page, you simply need to enter the URI path of the page and select the layout you want to assign to it.
You also have the option of caching the page and/or publishing. The <a href="<?=user_guide_url('modules/fuel/layouts')?>">layout</a> 
selected determines what form fields appear for you to edit. PHP code is not <a href="<?=user_guide_url('general/security')?>">allowed by default</a>, however you can use FUEL's
<a href="<?=user_guide_url('parsing')?>">parsing syntax</a>. Page variables can be <a href="<?=user_guide_url('general/inline-editing')?>">edited inline</a> 
if their layout uses the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_var</a> function to set the variables locations in the layout.

</p>
<img src="<?=img_path('examples/screen_page_edit.jpg', 'user_guide')?>" class="screen" />

<h2>Importing Existing Views</h2>
<p>If you are using <a href="<?=user_guide_url('general/opt-in-controllers')?>">Opt-in Controllers</a>, 
a view file that matches the URI location will trigger a prompt to import that view to edit if the modified date is after the pages last modified date.
This is a convenient way to make edits outside of the admin interface and import them.</p>
<img src="<?=img_path('examples/screen_page_import.jpg', 'user_guide')?>" class="screen" />
