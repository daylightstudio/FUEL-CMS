<?=js('SeoController', 'seo')?>

<div id="main_top_panel">
	<h2 class="ico ico_tools_seo"><a href="<?=fuel_url('tools')?>"><?=lang('section_tools')?></a> &gt; <?=lang('module_page_analysis')?></h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
		<p class="instructions"><?=lang('seo_page_analysis_instructions')?></p>
		<form action="<?=fuel_url('tools/seo')?>" method="post" id="form">
			<?=site_url()?><?=$this->form->select('page', $pages_select, $this->input->post('page'))?>
			<div style="text-align: center; margin-top: 10px;" class="buttonbar">
				<ul>
					<li class="end"><a href="#" class="ico ico_tools_seo" id="submit_page_analysis"><?=lang('btn_analyze')?></a></li>
				</ul>
			</div>
		</form>
		
		<div class="clear"></div>

		<div id="results" style="padding-top: 20px;">
			<h2><a href="<?=$url?>" target="_blank"><?=$url?></a></h2>
			<?php 
				if (!empty($results)){
				
					foreach($results as $tag => $vals){
						echo "<h3>".$tag."</h3>\n";
						echo "<ul class=\"nobullets\">\n";
						if (is_array($vals))
						{
							foreach($vals as $key => $val){
								echo "<li>";
								if (!is_int($key)) echo $key.': ';
								echo $val;
								echo "</li>\n";
							}
						}
						else
						{
							echo "<li>".$vals."</li>";
						}
						echo "</ul>\n";
					
					}
				}
			 ?>
		</div>
		
	</div>
	
</div>