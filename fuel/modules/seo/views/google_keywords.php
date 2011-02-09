<?=js('SeoController', 'seo')?>
<div id="main_top_panel">
	<h2 class="ico ico_tools_seo_google_keywords"><a href="<?=fuel_url('tools')?>">Tools</a> &gt; Google Keywords</h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
<?php $this->load->module_view(FUEL_FOLDER, '_blocks/notifications'); ?>
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
		<p class="instructions">
			<?=lang('seo_google_keywords_instructions')?>
		</p>
		<form action="<?=fuel_url('tools/seo/google_keywords')?>" method="post" id="form">
			<div class="float_left"><label for="domain"><?=lang('seo_label_domain')?></label> <?=$this->form->text('domain', $domain)?></div>
			<div class="float_left">
				<label for="keywords" style="padding-left: 20px;"><?=lang('seo_label_keywords')?></label> 
				<?php if (is_array($keywords)){?>
					<?=$this->form->select('keywords', $keywords, '')?>
				<?php } else { ?>
					<?=$this->form->text('keywords', $keywords, 'size="30"')?>
				<?php } ?>
			</div>
			<div class="float_left btn" style="margin: -1px 0 0 10px;">
			
				<?=$this->form->submit('Submit Keywords', 'submit_keywords', 'style="height: 0px; width: 0px;"')?>
			
				<a href="#" class="ico ico_tools_seo_google_keywords" id="submit_google_keywords"><?=lang('btn_submit_keywords')?></a>
			</div>				
			<div class="clear"></div>
		</form>

		<br />
		<div id="keyword_loader" class="loader hidden float_left"></div>
		<div id="results">

		</div>

	</div>
	
</div>