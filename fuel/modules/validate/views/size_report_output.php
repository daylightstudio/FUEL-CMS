<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Validate Links Output</title>
	<meta http-equiv="X-UA-Compatible" content="IE=7" /> <!-- for ie8 compatibility -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en-us" />
	<meta name="ROBOTS" content="ALL" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="MSSmartTagsPreventParsing" content="true" />
	<?=css('validate_results', 'validate')?>
	<style type="text/css" media="screen">
		li { line-height: 20px; }
		span.num { display: none; }
		h2#total { float: left; }
		div#summary_key { float: right; margin-right: 10px; }
	</style>
</head>
<body>
<div class="summary">
	<h1><a href="<?=$link?>"><?=$link?></a></h1>
	<h2 id="total">Approximate Total File Size 
		<?php if (($total_kb/1000) < 0) {?>
			<span class="error" id="total_error"><?=byte_format($total_kb)?></span>
		<?php } else if (($total_kb/1000) >= $config_limit AND ($total_kb/1000) > 0) {?>
			<span class="warning" id="total_warn"><?=byte_format($total_kb)?></span>
		<?php } else { ?>
			<span class="success" id="total_ok"><?=byte_format($total_kb)?></span>
		<?php } ?>
	</h2>
	<div id="summary_key">
		<span class="error"><span class="num"><?=count($invalid)?></span>Invalid</span>
		<span class="warning">File size is 0 OR &gt;= <?=$config_limit?>KB</span>
		<span class="success">File size is &lt; <?=$config_limit?>KB</span>
	</div>
</div>

<div class="clear"></div>

<h2>Resources and File Sizes</h2>
<ul>
	<?php foreach($invalid as $link) { ?>
	<li><a href="<?=$link?>" target="_blank"><?=$link?></a> <span class="error">Invalid</span></li>
	<?php } ?>
	<?php foreach($filesize_range['warn'] as $link => $filesize) { ?>
	<li><a href="<?=$link?>" target="_blank"><?=$link?></a> <span class="warning"><?=byte_format($filesize)?></span></li>
	<?php } ?>
	<?php foreach($filesize_range['ok'] as $link => $filesize) { ?>
	<li><a href="<?=$link?>" target="_blank"><?=$link?></a> <span class="success"><?=byte_format($filesize)?></span></li>
	<?php } ?>
</ul>

	
</body>
</html>


