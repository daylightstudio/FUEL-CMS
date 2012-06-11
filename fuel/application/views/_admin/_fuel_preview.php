<?php 
$vars['body'] = '<div class="preview_body">'.markdown(strip_javascript($body)).'</div>';
$this->load->view('_layouts/main', $vars);
//echo $body;
?>