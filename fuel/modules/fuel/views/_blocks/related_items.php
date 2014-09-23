<?php

if ( ! empty($related_items) && ((is_array($related_items) && current($related_items)) OR is_string($related_items)) OR ! empty($versions) && ! $this->fuel->admin->is_inline())
{
	echo '<div id="related_items">';

	if (isset($action) && $action == 'edit')
	{
		if ( ! empty($versions))
		{
			echo '
			<div class="versions">'.$this->form->select('fuel_restore_version', $versions, '', '', lang('label_restore_from_prev')).'</div>'.
			$this->form->hidden('fuel_restore_ref_id', $id);
		}
	}

	if (is_array($related_items))
	{
		$this->load->module_view(FUEL_FOLDER, '_blocks/related_items_array');
	}
	else if (is_string($related_items))
	{
		echo $related_items;
	}

	echo '</div>';
}