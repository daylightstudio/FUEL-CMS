<script type="text/javascript">
//<![CDATA[
	var html = '';
	<?php if ($this->fuel->auth->has_permission('tools/backup') AND $this->fuel->auth->accessible_module('backup')) : ?>
	html = '<p class="blue ico ico_info"><?=lang('data_backup_dashboard')?><?php if (!empty($last_backup_date)) : ?>  (<?=lang('data_last_backup').' '.$last_backup_date?>)<?php endif; ?>.</p>';
	<?php endif; ?>
	
	// put it in the notification bar
	$('#notification').html(html);
//]]>
</script>