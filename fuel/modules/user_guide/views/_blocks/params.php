<?php if (!empty($params)) : ?>
<h3>Parameters</h3>
<pre>
<?php 
if (!empty($params))
{
	foreach($params as $param) :
	echo "* @param ".$param."\n";
	endforeach;
}
?>
</pre>
<?php endif; ?>