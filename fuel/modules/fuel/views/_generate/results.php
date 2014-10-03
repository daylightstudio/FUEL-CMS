<div id="fuel_main_content_inner">
	<h3>Created</h3>
	<?php

	if ( ! empty($created))
	{
		echo '<ul class="nobullets">';
		foreach ($created as $val) echo '<li>' . $val . '</li>';
		echo '</ul>';
	}
	else
	{
		echo '<p>There were no files created.</p>';
	}

	if ( ! empty($modifed))
	{
		echo 'MODIFIED <ul class="nobullets">';
		foreach ($created as $val) echo '<li>' . $val . '</li>';
		echo '</ul>';
	}

	if ( ! empty($errors))
	{
		echo '<h3>Errors</h3><ul class="nobullets error">';
		foreach ($errors as $val) echo '<li>' . $val . '</li>';
		echo '</ul>';
	}

	?>
</div>