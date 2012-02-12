<?php if (!empty($helper)) : ?>

<h1><?=$helper?></h1>
<?php if (isset($comments[1]) AND $comments[1]->tags('package')) : ?>
<?=$comments[1]->description(array('long')); ?>
<?php endif; ?>
<br />
<br />
<br />
<h1>Function Reference</h1>
<?php 
foreach($helpers as $function => $function_obj) :
	$comment = $function_obj->comment;
	$comment->add_filter($user_guide_links_func);
	$example = $comment->example();
	$description = $comment->description(array('periods', 'one_line', 'markdown'));

?>

<?=$this->fuel->user_guide->block('function', array('function' => $function_obj)) ?>
<p><?=$description?></p>

<?=$this->fuel->user_guide->block('params', array('comment' => $comment)) ?>

<?=$this->fuel->user_guide->block('example', array('example' => $example)) ?>

<?php endforeach; ?>

<?php endif; ?>