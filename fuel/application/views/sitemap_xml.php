<?php
/***************************************************************
ATTENTION: To use this dynamic sitemap, you must uncomment the 
line in the applications/config/routes.php regarding the sitemap
**************************************************************/

fuel_set_var('layout', '');
$default_frequency = 'Monthly';
$nav = fuel_nav(array('return_normalized' => TRUE));

/***************************************************************
Add any dynamic pages and associate them to the $nav array here:
**************************************************************/

$CI->load->model('projects_model');
$projects = $CI->projects_model->find_all(); // won't filter on published because they all should be'

// add project pages
foreach($projects as $project)
{
	$key = 'showcase/project/'.$project->slug;
	$nav[$key] = array('location' => $key);
}



/**************************************************************/

if (empty($nav)) show_404();
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach($nav as $uri=>$page) { ?>
	<?php if(isset($page['location'])): ?>
		<url>
			<loc><?=site_url($page['location'])?></loc>
			<?php if (!empty($page['frequency'])) : ?><changefreq><?=$default_frequency?></changefreq><?php endif; ?>
		</url>	
	<?php elseif (is_string($page)): ?>
	<url>
		<loc><?=site_url($page)?></loc>
		<changefreq><?=$default_frequency?></changefreq>
	</url>
	<?php endif; ?>

<?php } ?>
</urlset>