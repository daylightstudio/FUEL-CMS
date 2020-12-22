<?php 
$config['config_class'] = 'HTMLPurifier_HTML5Config';// For HTML 5 compatibility issues https://github.com/xemlock/htmlpurifier-html5 
$config['settings'] = array(

	// Default setting is used for basic usage including the auto encoding database fields (if the auto_encode_entities property is set on the model which it is by default)
	'default' => array(
		//'HTML.Trusted'             => TRUE, // For Javascript... must also add 'script' to HTML.Allowed
		//'HTML.SafeIframe'          => TRUE, // For iframes
		//'URI.SafeIframeRegexp'     => '%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%',
		'Attr.EnableID'            => TRUE,
		'Attr.AllowedFrameTargets' => array('_blank'),
		'HTML.Allowed'             => 'div[id],b,strong,i,em,a[href|title|target],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
		//'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,float,margin',
		'AutoFormat.AutoParagraph' => FALSE, // This will cause errors if you globally apply this to input being saved to the database so we set it to false.
		'AutoFormat.RemoveEmpty'   => TRUE,
		'HTML.Doctype'             => 'HTML5'
	),

	// Can be used with html_purify function (e.g. html_purify($str, 'comment'))
	'comment' => array(
		'HTML.Doctype'             => 'XHTML 1.0 Strict',
		'HTML.Allowed'             => 'p,a[href|title|target],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike',
		'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,float,margin',
		'AutoFormat.AutoParagraph' => TRUE, 
		'AutoFormat.Linkify'       => TRUE,
		'AutoFormat.RemoveEmpty'   => TRUE,
	),

	// Can be used with html_purify function (e.g. html_purify($str, 'youtube'))
	'youtube' => array(
		'HTML.SafeIframe'          => TRUE,
		'URI.SafeIframeRegexp'     => '%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%',
	)
);
