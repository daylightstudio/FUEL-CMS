<h1><?=$class->friendly_name()?> Class</h1>
<p><?=$class->comment()->description()?></p>

<?=$this->fuel->user_guide->block('properties', array('class' => $class)) ?>

<h1>Function Reference</h1>
<?php 

$class_name = $class->name;
foreach($class->methods() as $method => $method_obj) :
	$comment = $method_obj->comment;
	$parameters = $method_obj->params();
	$example = $comment->example();
	$description = $comment->description();
	$comment_params = $comment->tags('param');
	$comment_return = $comment->tags('return');
?>	
<?=$this->fuel->user_guide->block('function', array('function' => $method_obj, 'prefix' => '$this->'.strtolower($class_name).'->')) ?>

<p><?=$description?></p>

<?=$this->fuel->user_guide->block('return', array('return' => $comment_return)) ?>

<?=$this->fuel->user_guide->block('params', array('params' => $comment_params)) ?>

<?=$this->fuel->user_guide->block('example', array('example' => $example)) ?>

<?php endforeach; ?>


