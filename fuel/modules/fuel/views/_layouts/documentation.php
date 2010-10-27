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

<br clear="all" />


<!-- START CONTENT -->
<div id="content">

<?=$body?>

</div>
<!-- END CONTENT -->


<script language="javascript">
SyntaxHighlighter.config['clipboardSwf'] = '<?=js_path('clipboard.swf', 'user_guide')?>';
SyntaxHighlighter.defaults['gutter'] = false;
SyntaxHighlighter.all();
</script>

</body>
</html>