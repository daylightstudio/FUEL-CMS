<?php 
$config['settings'] = array(
	'default' => array(
		'HTML.Doctype'             => 'XHTML 1.0 Strict',
		'HTML.Allowed'             => 'div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
		//'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,float,margin',
		'AutoFormat.AutoParagraph' => false, // This will cause errors if you globally apply this to input being saved to the database so we set it to false.
		'AutoFormat.RemoveEmpty'   => true,
	),
	'comment' => array(
		'HTML.Doctype'             => 'XHTML 1.0 Strict',
		'HTML.Allowed'             => 'p,a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike',
		'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,float,margin',
		'AutoFormat.AutoParagraph' => true, 
		'AutoFormat.Linkify'       => true,
		'AutoFormat.RemoveEmpty'   => true,
	),
	'youtube' => array(
		'HTML.SafeIframe'          => 'true',
		'URI.SafeIframeRegexp'     => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
	)
);
