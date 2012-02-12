<?php if ($class->properties()) : ?>
<h1>Properties Reference</h1>

<table border="0" cellspacing="1" cellpadding="0" class="tableborder">
	<tbody>
		<tr>
			<th>Property</th>
			<th>Default Value</th>
			<th>Description</th>
		</tr>
	<?php foreach($class->properties() as $prop => $prop_obj) : ?>
		<tr>
			<td><strong><?=$prop?></strong></td>
			<td>
				<?php
				if (empty($prop_obj->value))
				{
					echo 'none';
				}
				else if (is_object($prop_obj->value))
				{
					echo 'object';
				}
				else if (is_string($prop_obj->value))
				{
					echo htmlentities($prop_obj->value);
				} 
				else if (!empty($prop_obj->value))
				{
					echo $prop_obj->value;
				}
				?>
			</td>
			<td><?=$prop_obj->comment->description()?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<br />
<br />
<?php endif; ?>