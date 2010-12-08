<?=js('SeoController', 'seo')?>
<div id="main_top_panel">
	<h2 class="ico ico_tools_seo_google_keywords"><a href="<?=fuel_url('tools')?>">Tools</a> &gt; Google Keywords</h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
		<p class="instructions">
			Test the rankings of certain keywords for your domain.
		</p>
		<form action="<?=fuel_url('tools/seo/google_keywords')?>" method="post" id="form">
			<div class="float_left"><label for="domain">Domain:</label> <?=$this->form->text('domain', $domain)?></div>
			<div class="float_left">
				<label for="keywords" style="padding-left: 20px;">Keywords:</label> 
				<?php if (is_array($keywords)){?>
					<?=$this->form->select('keywords', $keywords, '')?>
				<?php } else { ?>
					<?=$this->form->text('keywords', $keywords, 'size="30"')?>
				<?php } ?>
				<?=$this->form->submit('Submit Keywords', 'submit_keywords')?>
			</div>
			<div class="clear"></div>
		</form>

		<br />
		<div id="keyword_loader" class="loader hidden float_left"></div>
		<div id="results">

		</div>

	</div>
	
</div>