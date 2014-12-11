<?php
$preview_body = parse_template_syntax(auto_typography($body));
// $vars['body'] = '<div class="preview_body">'.markdown(strip_javascript($preview_body)).'</div>';
$vars['body'] = '<div class="preview_body">'.$preview_body.'</div>';
$this->load->view('_layouts/main', $vars);
//echo $preview_body;
?>