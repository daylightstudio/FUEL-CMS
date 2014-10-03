<?php 
// set some render variables based on what is in the panels array
$no_menu = ( ! $this->fuel->admin->has_panel('nav')) ? TRUE : FALSE;
$no_titlebar = ( ! $this->fuel->admin->has_panel('titlebar')) ? TRUE : FALSE;
$no_actions = ( ! $this->fuel->admin->has_panel('actions') OR empty($actions)) ? TRUE : FALSE;
$no_notification = ( ! $this->fuel->admin->has_panel('notification')) ? TRUE : FALSE;

$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header');

echo '
<body>
	<div id="fuel_body"'.(($this->fuel->admin->ui_cookie('leftnav_hide') === '1') ? ' class="nav_hide"' : '').'>';

		// Top menu panel
		if ($this->fuel->admin->has_panel('top')) $this->load->module_view(FUEL_FOLDER, '_blocks/fuel_top');

		// Left menu panel
		if ($this->fuel->admin->has_panel('nav')) $this->load->module_view(FUEL_FOLDER, '_blocks/nav');

		echo '<div id="fuel_main_panel'.($no_menu ? '_compact' : '').'">';
	
			// Breadcrumb/title bar panel
			if ($this->fuel->admin->has_panel('titlebar')) $this->load->module_view(FUEL_FOLDER, '_blocks/titlebar');

			echo $this->form->open('action="'.$form_action.'" method="'.(( ! empty($form_method)) ? $form_method : 'post').'" id="form" enctype="multipart/form-data"');
		
				if ($this->fuel->admin->has_panel('actions') && ! empty($actions))
				{
					// Action panel
					echo ' <div id="fuel_actions">';
					if ( ! empty($actions)) echo $actions;
					echo '</div>';
				}

				if ($this->fuel->admin->has_panel('notification'))
				{
					// Notification panel
					echo '<div id="fuel_notification" class="notification">';
					if ( ! empty($notifications)) echo $notifications;
					if ( ! empty($pagination)) echo '<div id="pagination">'.$pagination.'</div>';
					echo '</div>';
				}

				$main_content_class = '';

				if ($no_actions && $no_notification)
				{
					$main_content_class = 'noactions_nonotification';
				}
				else if ($no_actions && $no_titlebar)
				{
					$main_content_class = 'noactions_notitlebar';
				}
				else if ($no_titlebar)
				{
					$main_content_class = 'notitlebar';
				}
				else if ($no_actions)
				{
					$main_content_class = 'noactions';
				}

				echo '
				<div id="fuel_main_content'.($no_menu ? '_compact' : '').'"'.( ! empty($main_content_class) ? ' class="'.$main_content_class.'"' : '').'>'.
					$body.
					$this->form->hidden('fuel_inline', (int)$this->fuel->admin->is_inline()).'
				</div>'.
			$this->form->close().'
		</div>
	</div>
	<div id="fuel_modal" class="jqmWindow"></div>'.
	$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_footer').'
</body>
</html>';