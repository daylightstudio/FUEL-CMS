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