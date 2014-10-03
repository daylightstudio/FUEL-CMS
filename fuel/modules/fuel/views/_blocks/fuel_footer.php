<?php

echo '
<script type="text/javascript">'.
	$this->load->module_view(FUEL_FOLDER, '_blocks/fuel_header_jqx', array(), TRUE).'
</script>';

echo js('jqx/jqx', 'fuel');

$fuel_js = $this->fuel->config('fuel_javascript');

foreach ($fuel_js as $m => $j) echo js(array($m => $j))."\n\t";

foreach ($js as $m => $j) echo js(array($m => $j))."\n\t";

if ( ! empty($this->js_controller))
{
	echo '<script type="text/javascript">';

	if ($this->js_controller != 'fuel.controller.BaseFuelController')
	{
		echo "jqx.addPreload('fuel.controller.BaseFuelController');";
	}

	echo "
		jqx.init('$this->js_controller', ".json_encode($this->js_controller_params).", '$this->js_controller_path');
	</script>";
}