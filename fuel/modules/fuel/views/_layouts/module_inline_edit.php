<div>
	<h2 class="module_name"><?=$this->module_name?>: <?=ucfirst($action)?></h2>
	<?=$this->form->open(array('method' => 'post'))?>
	<a name="inline_errors"></a>
	<div class="notification inline_errors"></div>
	<?=$form?>
	
	<?php if ($action != 'create'){ ?>
	<a href="#" class="delete">Delete</a>
	<?php } ?>
	
	<div class="buttonbar save_cancel">
		<ul>
			<li class="start end"><a href="#" class="ico ico_cancel modal_cancel">Cancel</a></li>
			<li class="start end"><a href="#" class="ico ico_save">Save</a></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<?=$this->form->close()?>


</div>