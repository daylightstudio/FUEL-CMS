<?php

$this->load->module_view(FUEL_FOLDER, '_blocks/related_items');

echo '<div id="notification_extra" class="notification">';

	if ( ! empty($data['published']) && !is_true_val($data['published']))
	{
		echo '<div class="warning ico ico_warn">'.lang('pages_not_published').'</div>';
	}
	
	if ( ! empty($routes))
	{
		echo '
		<div class="warning ico ico_warn">' .
			lang('page_route_warning', APPPATH . 'config/routes.php');

			foreach ($routes as $val)
			{
				echo '
				<ul>
					<li>'.$val.'</li>
				</ul>';
			}

		echo '</div>';
	}

	if ($uses_controller)
	{
		echo '
		<div class="warning ico ico_warn">'.
			lang('page_controller_assigned', $view_twin).'
		</div>';
	}

echo '</div>';

if ($import_view)
{
	echo '
	<div class="warning jqmWindow jqmWindowShow" id="view_twin_notification">
		<div class="modal_content_inner">
			<p>'.lang('page_updated_view', $view_twin).'</p>
			<div class="buttonbar" id="yes_no_modal">
				<ul>
					<li class="unattached"><a href="#" class="ico ico_no" id="view_twin_cancel">'.lang('page_no_upload').'</a></li>
					<li class="unattached"><a href="#" class="ico ico_yes" id="view_twin_import">'.lang('page_yes_upload').'</a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	</div>';
}

echo '
<div id="fuel_main_content_inner">
	<p class="instructions">'.$this->instructions.'</p>
	<div id="tab_page_variables">
		<h3>'.lang('page_information').'</h3>
		<div id="page_property_vars">'.$form.'</div>
		<h3>'.lang('page_layout_vars').'</h3>
		<div id="layout_vars">'.
			$layout_fields.'
			<div class="loader hidden"></div>
		</div>
	</div>
</div>';