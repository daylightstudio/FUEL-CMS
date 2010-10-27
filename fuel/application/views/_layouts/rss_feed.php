<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "
";
?>

<rss version="2.0"> 
	<channel> 
		<title><?=$title?></title> 
		<link><?=site_url($this->location)?></link> 
		<description><?=$description?></description> 
		<language><?=$language?></language> 
		<lastBuildDate><?=standard_date('DATE_ATOM', strtotime(fuel_block('rss', 'last_updated')))?></lastBuildDate>
		<?=fuel_block('rss', 'feed_items')?>

    </channel>
</rss>