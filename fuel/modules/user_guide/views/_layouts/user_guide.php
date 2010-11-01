<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FUEL CMS User Guide : Version <?=FUEL_VERSION?> : <?=$page_title?></title>

<?=css('userguide, fuel_override, shCore, shThemeDefault', 'user_guide')?>

<?=js('jquery')?>
<?=js('user_guide', 'user_guide')?>
<?=js('shCore, shBrushSql, shBrushCss, shBrushJScript, shBrushPhp','user_guide')?>

<meta http-equiv='expires' content='-1' />
<meta http-equiv= 'pragma' content='no-cache' />
<meta name='robots' content='all' />

</head>
<body>

<?php if ($use_nav) : ?>
<!-- START NAVIGATION -->
<div id="nav" style="display: none;">
	<div id="nav_inner">
		
	<?php $this->load->module_view(USER_GUIDE_FOLDER, '_blocks/toc')?>
		
	</div>
</div>
<div id="nav2"><a name="top"></a><a href="#" id="toc_toggle">Table of Content</a></div>
<div id="masthead">
	<h1>FUEL CMS  User Guide : Version <?=FUEL_VERSION?></h1>
</div>
<!-- END NAVIGATION -->
<?php endif; ?>

<?php if ($use_breadcrumb) : ?>
<!-- START BREADCRUMB -->
<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
<tr>
<td id="breadcrumb">
<a href="<?=user_guide_url()?>">User Guide Home</a> &nbsp;&#8250;&nbsp;
<?php if (!empty($sections)) : ?>
<?php foreach($sections as $key => $val) : ?>
<a href="<?=user_guide_url($val)?>"><?=$key?></a> &nbsp;&#8250;&nbsp;
<?php endforeach; ?>
<?php endif; ?>

<?=$page_title?>
</td>
<?php if ($use_search) : ?>
<td id="searchbox"><form method="get" action="http://www.google.com/search"><input type="hidden" name="as_sitesearch" id="as_sitesearch" value="getfuelcms.com" />Search Project User Guide&nbsp; <input type="text" class="input" style="width:200px;" name="q" id="q" size="31" maxlength="255" value="" />&nbsp;<input type="submit" class="submit" name="sa" value="Go" /></form></td>
<?php endif; ?>
</tr>
</table>
<!-- END BREADCRUMB -->
<?php endif; ?>
<br clear="all" />


<!-- START CONTENT -->
<div id="content">

<?=$body?>

</div>
<!-- END CONTENT -->


<?php if ($use_footer) : ?>
<div id="footer">
<p>
  &nbsp;&middot;  
<a href="#top">Top of Page</a>  &nbsp;&middot;  
<a href="<?=user_guide_url()?>">User Guide Home</a>  &nbsp;&middot;  
</p>
<p><a href="http://getfuelcms.com">FUEL CMS</a> &nbsp;&middot;&nbsp; &copy; Copyright <?=date('Y')?> &nbsp;&middot;&nbsp; <a href="http://thedaylightstudio.com/">Daylight Studio</a></p>
</div>
<?php endif; ?>

<script language="javascript">
SyntaxHighlighter.config['clipboardSwf'] = '<?=js_path('clipboard.swf', 'user_guide')?>';
SyntaxHighlighter.defaults['gutter'] = false;
SyntaxHighlighter.all();
</script>

</body>
</html>