<script type="text/javascript">
//<![CDATA[
	var html = '';
	<?php if ($this->fuel_auth->has_permission('tools/backup')) : ?>
	html = '<p class="blue ico ico_info">Remember to preiodically <a href="<?=fuel_url('tools/backup')?>">backup your database</a><?php if (!empty($last_backup_date)) { ?> (last backup <?=$last_backup_date?>)<?php } ?>.</p>';
	<?php endif; ?>
	
	// put it in the notification bar
	$('#notification').html(html);
//]]>
</script>