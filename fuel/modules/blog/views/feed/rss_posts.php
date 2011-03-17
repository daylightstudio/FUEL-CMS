<?php echo str_replace(';', '', '<?xml version="1.0" encoding="UTF-8"?>'); ?> 
<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">
	<channel>
		<title><?php echo $title; ?></title>
		<link><?php echo $link; ?></link>
		<description><?php echo $description; ?></description>
		<pubDate><?php echo standard_date('DATE_RSS', strtotime($last_updated)); ?></pubDate>
		<language><?php echo $this->fuel_blog->language(TRUE)?></language>
	    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
	
	    <dc:rights>Copyright <?php echo gmdate('Y', strtotime($last_updated)); ?></dc:rights>
	    
	    <?php if (!empty($posts)){ ?>
		    <?php foreach ($posts as $post){ ?>
		    <item>
				<title><?php echo xml_convert($post->title); ?></title>
				<link><?php echo $post->url; ?></link>
				<guid><?php echo $post->url; ?></guid>
				<description><![CDATA[
				<?php echo $post->excerpt_formatted; ?>
		      	]]>
		      	</description>
		     </item>
		    <?php } ?>
	    <?php } ?>
	    
	</channel>
</rss>