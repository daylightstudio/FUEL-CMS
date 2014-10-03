<?php
echo '<div id="notification_extra" class="notification">';

if (isset($this->model) && method_exists($this->model, 'notification') && $this->model->notification($data))
{
	echo '<div class="warning ico ico_warn">'.$this->model->notification($data).'</div>';
}
else if (isset($data['published']) && ! is_true_val($data['published']))
{
	echo '<div class="warning ico ico_warn">'.lang('warn_not_published').'</div>';
}
else if (isset($data['active']) && ! is_true_val($data['active']) && isset($this->model))
{
	echo '<div class="warning ico ico_warn">'.lang('warn_not_active', $this->model->singular_name()).'</div>';
}

echo '</div>';