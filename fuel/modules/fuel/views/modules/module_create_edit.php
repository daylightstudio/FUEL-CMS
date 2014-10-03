<?php

//Related items
$this->load->module_view(FUEL_FOLDER, '_blocks/related_items');

// Notification extra
$this->load->module_view(FUEL_FOLDER, '_blocks/notification_extra');

// Warning window
$this->load->module_view(FUEL_FOLDER, '_blocks/warning_window');

echo '<div id="fuel_main_content_inner">';

	if ( ! empty($instructions)) echo '<p class="instructions">'.$instructions.'</p>';

	echo $form;

echo '</div>';