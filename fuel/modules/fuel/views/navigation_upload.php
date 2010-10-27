<div id="main_top_panel">
	<h2 class="ico ico_navigation"><a href="<?=fuel_url($this->module_uri)?>"><?=$this->module_name?></a> &gt; Import</h2>
</div>
<div class="clear"></div>


<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

<div id="main_content_inner">

	<?=$this->form->open(array('id' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'))?>
	
	<p class="instructions">Select a navigation group and upload a file to import below. The file should contain the PHP array variable <strong>$nav</strong>. For a reference of the array format, please consult the <a href="http://www.getfuelcms.com/user_guide" target="_blank">user guide</a>:<br/> </p>
	<?=$form?>

	<br />
	<br />

	<div class="buttonbar">
		<ul>
			<li class="end"><a href="<?=fuel_url($this->module_uri.'/')?>" class="ico ico_no">No, don't upload it</a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit">Yes,  upload it</a></li>
		</ul>
	</div>

	<?=$this->form->close()?>
	
	
</div>

