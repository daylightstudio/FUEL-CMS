<?php
// look at the views/sitemap_xml.php file for another way to get pages using fuel_nav()
$this->load->module_model(FUEL_FOLDER, 'pages_model');
$pages = $this->pages_model->find_all(array('published' => 'yes'));

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($pages as $page) { ?>
<url>
	<loc><?=site_url($page->location)?></loc>
	<lastmod><?=$page->last_modified?></lastmod>
	<changefreq><?=fuel_var('frequency')?></changefreq>
</url>
<?php } ?>
</urlset>