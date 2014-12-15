<?php 
$config['share_urls'] = array(
	'twitter'    => 'http://twitter.com/share?url={url|link}&amp;text={title}&amp;via={source:'.CI()->fuel->config('site_name').'}',
	'facebook'   => 'https://www.facebook.com/sharer.php?u={url|link}&amp;t={title}&amp;d={description|summary|excerpt|content}',
	'googleplus' => 'https://plus.google.com/share?url={url|link}',
	'linkedin'   => 'http://www.linkedin.com/shareArticle?mini=true&amp;url={url|link}&amp;title={title}&amp;summary={description|summary|excerpt|content}&amp;source={source:'.CI()->fuel->config('site_name').'}',
	'email'      => 'mailto:?subject={title}&amp;body={description|summary|excerpt|content}:%0A{url|link}',
	);

