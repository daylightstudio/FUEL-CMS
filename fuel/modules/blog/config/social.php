<?php 

// used for social bookmarks
$config['social']['bookmarks'] = array();
$config['social']['bookmarks']['Digg'] = 'http://digg.com/submit?phase=2&amp;url={url}&amp;title={title}';
$config['social']['bookmarks']['Technorati'] = 'http://technorati.com/faves?add={url}';
$config['social']['bookmarks']['del.icio.us'] = 'http://del.icio.us/post?url={url}&amp;title={title}';
$config['social']['bookmarks']['Stumbleupon'] = 'http://www.stumbleupon.com/submit?url={url}&amp;title={title}';
$config['social']['bookmarks']['reddit'] = 'http://reddit.com/submit?url={url}&amp;title={title}';
$config['social']['bookmarks']['Furl'] = 'http://www.furl.net/storeIt.jsp?t={title}&amp;u={url}';

// facebook
$config['social']['facebook_recommend'] = 'http://www.facebook.com/plugins/like.php?&amp;layout=standard&amp;show-faces=true&amp;width=450&amp;action=like&amp;colorscheme=evil&amp;href={url}';
$config['social']['facebook_share'] = 'http://widgets.fbshare.me/files/fbshare.js';

// digg
$config['social']['digg'] = 'http://widgets.digg.com/buttons.js';

// tweetme
$config['social']['tweetme'] = 'http://tweetmeme.com/i/scripts/button.js';

// stumbleupon
$config['social']['stumbleupon'] = 'http://www.stumbleupon.com/hostedbadge.php?s=5';
