<?php 
fuel_set_var('layout', '');
$output = $this->fuel->pages->render($alias, array(), array('render_mode' => 'cms'), TRUE);
// show 404 if output is explicitly set to FALSE
if ($output === FALSE)
{
	// do any redirects... will exit script if any
	redirect_404();
}
echo $output;
?>