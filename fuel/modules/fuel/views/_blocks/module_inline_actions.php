<div class="buttonbar" id="action_btns">
	<ul>
	<?php

	if (isset($action) && $action == 'edit')
	{
		if ($this->fuel->auth->module_has_action('save'))
		{
			echo '<li><a href="'.fuel_url($this->module_uri.'/inline_edit').'" class="ico ico_save save" title="'.$keyboard_shortcuts['save'].' to save">'.lang('btn_save').'</a></li>';
		}
			
		if ($this->fuel->auth->module_has_action('publish') && $this->fuel->auth->has_permission($this->permission, 'publish'))
		{
			if ( ! empty($publish))
			{
				echo '<li><a href="'.fuel_url($this->module_uri.'/inline_edit').'" class="ico ico_'.strtolower($publish).' '.strtolower($publish).'_action">'.lang('btn_'.strtolower($publish)).'</a></li>';
			}
		}

		if ($this->fuel->auth->module_has_action('activate') && $this->fuel->auth->has_permission($this->permission, 'activate'))
		{
			if ( ! empty($activate))
			{
				echo '<li><a href="#" class="ico ico_'.strtolower($activate).' '.strtolower($activate).'_action">'.lang('btn_'.strtolower($activate)).'</a></li>';
			}
		}

		if ($this->fuel->auth->module_has_action('delete') && $this->fuel->auth->has_permission($this->permission, 'delete'))
		{
			echo '<li><a href="'.fuel_url($this->module_uri.'/inline_delete/'.$id).'" class="ico ico_delete delete_action"><'.lang('btn_delete').'</a></li>';
		}
			
		if ($this->fuel->auth->module_has_action('duplicate'))
		{
			echo '<li><a href="'.fuel_url($this->module_uri.'/inline_create').'" class="ico ico_duplicate duplicate_action">'.lang('btn_duplicate').'</a></li>';
		}

		if ($this->fuel->auth->module_has_action('create'))
		{
			echo '<li class="end"><a href="'.fuel_url($this->module_uri.'/inline_create').'" class="ico ico_create">'.lang('btn_create').'</a></li>';
		}
	}
	else if ($action == 'create' && $this->fuel->auth->module_has_action('save'))
	{
		echo '<li class="end"><a href="'.fuel_url($this->module_uri.'/inline_create').'" class="ico ico_save save" title="'.$keyboard_shortcuts['save'].' to save">'.lang('btn_save').'</a></li>';
	}
	?>

	</ul>
</div>

<?php

if (isset($action) AND $action == 'edit')
{
	echo '<div id="filters">';

	if ( ! empty($versions))
	{
		echo '
		<form method="post" action="'.fuel_url($this->module_uri.'/restore').'" id="restore_form">
			<div class="versions">'.
				$this->form->select('version', $versions, '', '', lang('label_restore_from_prev')).
				$this->form->hidden('ref_id', $id).'
			</div>'.
		$this->form->close();
	}

	echo '</div>';
}