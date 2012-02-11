<h1><?=$helper?></h1>

<h1>Function Reference</h1>
<?php 
foreach($helpers as $function => $function_obj) :
	$comment = $function_obj->comment;
	$example = $comment->example();
	$description = $comment->description();

?>

<?=$this->fuel->user_guide->block('function', array('function' => $function_obj)) ?>
<p><?=$description?></p>

<?=$this->fuel->user_guide->block('params', array('comment' => $comment)) ?>

<?=$this->fuel->user_guide->block('example', array('example' => $example)) ?>

<?php endforeach; ?>