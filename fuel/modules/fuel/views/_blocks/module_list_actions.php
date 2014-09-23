<div id="filters">
	<table border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<?php

				if (empty($this->advanced_search))
				{
					echo '<td>';
					if ( ! empty($more_filters)) echo $more_filters;
					echo '</td>';
				}

				echo '
				<td><a href="'.fuel_url($this->module_uri.'/reset_page_state', FALSE).'" class="reset"></a></td>
				<td>
					<div class="search_input'.( ! empty($this->advanced_search) ? ' advanced' : '').'">'.
						$this->form->search('search_term', $params['search_term'], 'placeholder="'.lang('label_search').'"');

						if ( ! empty($this->advanced_search))
						{
							echo '
							<a href="#" id="adv-search-btn" title="'.lang('adv_search').'">
								<img src="'.fuel_url('modules/fuel/assets/images/th_arrow_desc.png').'" />
							</a>
							<div class="adv_search">
								<p><strong>'.lang('adv_search').'</strong></p>';

								if ( ! empty($more_filters)) echo $more_filters;

							echo '
								<p>'.
									$this->form->submit(lang('btn_search'), 'search').' &nbsp;&nbsp;
									<a href="#" id="adv-search-close">'.lang('viewpage_close').'</a> &nbsp;&nbsp;
									<a href="'.fuel_url($this->module_uri.'/reset_page_state', FALSE).'">'.lang('reset_search').'</a>
								</p>
							</div>';
						}

				echo '
					</div>
				</td>
				<td class="search">'.$this->form->submit(lang('btn_search'), 'search').'</td>
				<td class="show">
					<label for="limit">'.lang('label_show').'</label> '.
					$this->form->select('limit', $this->limit_options, $params['limit']).'
				</td>';

				?>
			</tr>
		</tbody>
	</table>
</div>

<div class="buttonbar" id="action_btns">
	<ul>
		<?php

		$create_url = ( ! empty($this->model->filters['group_id'])) ? $this->module_uri.'/create/'.$this->model->filters['group_id'] : $this->module_uri.'/create';

		if ( ! empty($tree))
		{
			echo '
			<li class="active"><a href="#" id="toggle_list" class="ico ico_table" title="'.$keyboard_shortcuts['toggle_view'].' to toggle view">'.lang('btn_list').'</a></li>
			<li><a href="#" id="toggle_tree" class="ico ico_tree" title="'.$keyboard_shortcuts['toggle_view'].' to toggle view">'.lang('btn_tree').'</a></li>';
		}

		if ( ! empty($this->list_actions))
		{
			foreach($this->list_actions as $action => $label)
			{
				$lang_key = str_replace('/', '_', $action);

				if ($this->fuel->auth->has_permission($this->permission, $action))
				{
					echo '<li>'.anchor(fuel_url($action), $label, array('class' => 'ico ico_'.$lang_key)).'</li>';
				}
			}
		}

		echo '
		<li><a href="#" class="ico ico_select_all">'.lang('btn_select_all').'</a></li>
		<li><a href="#" class="ico ico_precedence" id="rearrange">'.lang('btn_rearrange').'</a></li>';

		if ($this->fuel->auth->module_has_action('delete') && $this->fuel->auth->has_permission($this->permission, 'delete'))
		{
			echo '<li><a href="#" class="ico ico_delete" id="multi_delete">'.lang('btn_delete_multiple').'</a></li>';
		}

		if ($this->exportable && $this->fuel->auth->has_permission($this->permission, 'export'))
		{
			echo '<li><a href="'.fuel_url($this->module_uri.'/export').'" class="ico ico_export" id="export_data">'.lang('btn_export_data').'</a></li>';
		}

		if ($this->fuel->auth->module_has_action('create') && $this->fuel->auth->has_permission($this->permission, 'create'))
		{
			echo '<li class="end"><a href="'.fuel_url($create_url).'" class="ico ico_create">'.$this->create_action_name.'</a></li>';
		}

		?>
	</ul>
</div>

<?php

if ( ! empty($params['view_type'])) echo $this->form->hidden('view_type', $params['view_type']);