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
	
</head>
<body>
<?php if (!empty($error)){ ?>
<span class="error" id="total_invalid"><span id="total_invalid_num">404</span> Error</span>
<?php } else { ?>
<div id="edit_url"><?=$edit_url?></div>
<div class="summary">
<h1><a href="<?=$link?>"><?=$link?></a></h1>
<span class="success" id="total"><?=lang('validate_total_valid')?> <?=($total - count($invalid))?></span>
<span class="error" id="total_invalid"><?=lang('validate_total_invalid')?> <span id="total_invalid_num"><?=count($invalid)?></span></span>
</div>

<?php if (count($invalid)){ ?>
<h2>Invalid Links</h2>
<ul>
	<?php foreach($invalid as $link) { ?>
	<li class="invalid"><a href="<?=$link?>" target="_blank"><?=$link?></a></li>
	<?php } ?>
</ul>
<?php } ?>
<?php } ?>	
</body>
</html>


