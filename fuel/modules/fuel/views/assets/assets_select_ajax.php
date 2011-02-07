<form method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" id="ajax_form">
<?=$form?>
</form>
<div class="buttonbar" style="margin: 10px 0 0 40px">
	<ul>
		<li class="start end spacer"><a href="#" class="ico ico_no modal_cancel"><?=lang('btn_cancel')?></a></li>
		<li class="start end spacer"><a href="#" class="save ico ico_yes modal_cancel" id="submit"><?=lang('btn_ok')?></a></li>
	</ul>
</div>