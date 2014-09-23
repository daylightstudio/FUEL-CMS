<?php

echo '
<div class="buttonbar" id="action_btns">
	<ul>';

	if (isset($action) && $action == 'edit')
	{
		if ($this->fuel->auth->module_has_action('save'))
		{
			echo '<li><a href="#" class="ico ico_save save" title="'.$keyboard_shortcuts['save'].' to save">'.lang('btn_save').'</a></li>';
		}

		if ( ! empty($this->preview_path) && $this->fuel->auth->module_has_action('view'))
		{
			echo '
			<li>
				<a href="'.site_url($this->preview_path, false).'" class="ico ico_view key_view_action'.(( ! $this->fuel->config('view_in_new_window')) ? ' view_action' : '').'" title="'.$keyboard_shortcuts['view'].' to view" target="_blank">'.lang('btn_view').'</a>
			</li>';
		}

		if ($this->fuel->auth->module_has_action('publish') && $this->fuel->auth->has_permission($this->permission, 'publish'))
		{
			if ( ! empty($publish))
			{
				echo '<li><a href="#" class="ico ico_'.strtolower($publish).' '.strtolower($publish).'_action">'.lang('btn_'.strtolower($publish)).'</a></li>';
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
			echo '<li><a href="'.fuel_url($this->module_uri.'/delete/'.$id, true).'" class="ico ico_delete delete_action">'.lang('btn_delete').'</a></li>';
		}

		if ($this->fuel->auth->module_has_action('duplicate'))
		{
			echo '<li><a href="'.fuel_url($this->module_uri.'/create', true).'" class="ico ico_duplicate duplicate_action">'.lang('btn_duplicate').'</a></li>';
		}

		if ($this->fuel->auth->module_has_action('replace') && ! empty($others) && $this->fuel->auth->has_permission($this->permission, 'edit') && $this->fuel->auth->has_permission($this->permission, 'delete'))
		{
			echo '<li><a href="'.fuel_url($this->module_uri.'/replace/'.$id, true).'" class="ico ico_replace replace_action">'.lang('btn_replace').'</a></li>';
		}

		if ($this->fuel->auth->module_has_action('others'))
		{
			foreach ($this->item_actions['others'] as $other_action => $label)
			{
				if ($this->fuel->auth->has_permission($this->permission, $other_action))
				{
					$ico_key  = str_replace('/', '_', $other_action);
					$lang_key = url_title($label, 'underscore', true);

					if ($new_label = lang('btn_'.$lang_key)) $label = $new_label;

					echo '<li>'.anchor(fuel_url($other_action, true), $label, array('class' => 'submit_action ico ico_'.$ico_key)).'</li>';
				}
			}
		}

		if ($this->fuel->auth->module_has_action('create') && $this->fuel->auth->has_permission($this->permission, 'create'))
		{
			echo '<li class="end"><a href="'.fuel_url($this->module_uri.'/create', true).'" class="ico ico_create">'.lang('btn_create').'</a></li>';
		}
		else if ($action == 'create' && $this->fuel->auth->module_has_action('save'))
		{
			echo '<li class="end"><a href="#" class="ico ico_save save" title="'.$keyboard_shortcuts['save'].' to save">'.lang('btn_save').'</a></li>';
		}
	}

	echo '</ul>';

	if ( ! empty($others) && ! $this->fuel->admin->is_inline())
	{
		echo '
		<div id="other_items">'.
			$this->form->select('fuel_other_items', $others, '', '', lang('label_select_another'), array($id)).'
		</div>';
	}

echo '</div>';