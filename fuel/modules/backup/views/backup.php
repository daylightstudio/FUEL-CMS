<div id="main_top_panel">
	<h2 class="ico ico_tools_backup"><a href="<?=fuel_url('tools')?>">Tools</a> &gt; Backup Database</h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
	<p class="instructions">You are about to backup your database. This will download a gzip file from your browser that you can 
	save on your computer. 
	
	<?php if ($is_writable) : ?>
		It will also create a dated backup file on the web server in the directory:<br />
			<strong><em><?=$download_path?></em></strong></p>
	<?php else: ?>
		To save the zipped data on the server, you must make the following directory writable or change the directory in the fuel config file:<br />
			<strong><em><span class="error"><?=$download_path?></span></em>  (not writable)</strong></p>
	<?php endif; ?>
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	
	<div style="padding: 10px 0;"><?=$this->form->checkbox('include_assets', '1')?> <label for="include_assets">Include the assets folder?</label></div>
	
	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url('recent')?>" class="ico ico_no">No, don't back it up</a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit">Yes,  back it up</a></li>
		</ul>
	</div>
	<?=$this->form->hidden('action', 'backup')?>
	<?=$this->form->close()?>
	</div>

</div>