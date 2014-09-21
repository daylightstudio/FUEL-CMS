<div id="notification_extra" class="notification">
	<?php if (isset($this->model) AND method_exists($this->model, 'notification') AND $this->model->notification($data)) { ?>
		<div class="warning ico ico_warn"><?=$this->model->notification($data)?></div>
	<?php } elseif (isset($data['published']) AND !is_true_val($data['published'])) {?>
			<div class="warning ico ico_warn"><?=lang('warn_not_published')?></div>
	<?php } else if (isset($data['active']) AND !is_true_val($data['active']) AND isset($this->model)) {?>
		<div class="warning ico ico_warn"><?=lang('warn_not_active', $this->model->singular_name())?></div>
	<?php } ?>
</div>
