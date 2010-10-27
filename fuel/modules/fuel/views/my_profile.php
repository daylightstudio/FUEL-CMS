<div id="main_top_panel">
	<h2>My Profile</h2>
</div>
<div class="clear"></div>

<div id="action">

	<div class="buttonbar" id="actions">
		<ul>
			<li class="end"><a href="#" class="ico ico_save save" title="<?=$keyboard_shortcuts['save']?> to save">Save</a></li>
		</ul>
	</div>
	

</div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content">

	<div id="main_content_inner">

		<p class="instructions">Change your profile information below:</p>

		<form method="post" action="<?=fuel_url('my_profile/edit/')?>" id="form">
		<?=$form?>
		</form>
	
	</div>
</div>