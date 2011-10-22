<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('page_analysis_instructions')?></p>
	<form action="<?=fuel_url('tools/page_analysis')?>" method="post" id="form">
		<?=site_url()?><?=$this->form->select('page', $pages_select, $this->input->post('page'))?>
		<div style="text-align: center; margin-top: 10px;" class="buttonbar">
			<ul>
				<li class="end"><a href="#" class="ico ico_tools_page_analysis" id="submit_page_analysis"><?=lang('btn_analyze')?></a></li>
			</ul>
		</div>
	</form>
	
	<div class="clear"></div>

	<div id="results" style="padding-top: 20px;">
		<h2><a href="<?=$url?>" target="_blank"><?=$url?></a></h2>
		<?php 
			if (!empty($results))
			{
				
				foreach($results as $tag => $vals)
				{
					echo "<h3>".$tag."</h3>\n";
					echo "<ul class=\"nobullets\">\n";
					if (is_array($vals))
					{
						if (empty($vals)) echo '<li>'.lang('page_analysis_no_data').'</li>';
						
						foreach($vals as $key => $val)
						{
							echo "<li>";
							if (!is_int($key)) echo $key.': ';
							echo (!empty($val)) ? $val : lang('page_analysis_no_data');
							echo "</li>\n";
						}
					}
					else
					{
						echo "<li>";
						echo (!empty($vals)) ? $vals : lang('page_analysis_no_data');
						echo "</li>";
					}
					echo "</ul>\n";
				
				}
			}
			else
			{
				echo lang('page_analysis_no_data');
			}
		 ?>
	</div>
	
</div>