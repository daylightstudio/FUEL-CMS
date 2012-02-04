<script type="text/javascript">
//<![CDATA[
	__FUEL_LINKED_FIELDS = <?=json_encode($linked_fields)?>;
//]]>
</script>
<div>
	<h2 class="module_name"><?=$this->module_name?>: <?=ucfirst($action)?></h2>
	<?=$this->form->open(array('method' => 'post'))?>
	<a name="inline_errors"></a>
	<div class="notification inline_errors"></div>
	<?=$form?>
	
	
	<?php if ($action != 'create' AND $this->fuel_auth->has_permission($this->permission, 'delete')){ ?>
	<a href="#" class="delete"><?=lang('btn_delete')?></a>
	<?php } ?>
	
	<div class="buttonbar save_cancel">
		<ul>
			<li class="start end"><a href="#" class="ico ico_cancel modal_cancel"><?=lang('btn_cancel')?></a></li>
			<li class="start end"><a href="#" class="ico ico_save"><?=lang('btn_save')?></a></li>
		</ul>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<?=$this->form->close()?>


</div>