<?php 
$func_name = $function->name;
if (empty($prefix))
{
	$prefix = '';
}
$parameters = $function->params();
$num_parameters = count($parameters);

$params = array();
if ($num_parameters)
{
	foreach ($parameters as $param) :
		$param_str = '<var>';
		if ($param->is_optional()) : $param_str .= '['; endif;
		if (!$param->is_default_array()) : $param_str .= '\''; endif;
		$param_str .= $param->name;
		if (!$param->is_default_array()) : $param_str .= '\''; endif;
		if ($param->is_default_value_available()) : $param_str .= '='.$param->default_value(TRUE); endif;
		if ($param->is_optional()) : $param_str .= ']'; endif;
		$param_str .= '</var>';
		$params[] = $param_str; 
	endforeach;
}
?>

<h2 id="func_<?=strtolower($func_name)?>"><?=$prefix?><?=$func_name?>(<?=implode(', ', $params)  ?>)</h2>