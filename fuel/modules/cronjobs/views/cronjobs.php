<?=js('CronjobsController', 'cronjobs')?>
<div id="main_top_panel">
	<h2 class="ico ico_tools_cronjobs"><a href="<?=fuel_url('tools')?>">Tools</a> &gt; <?=ucfirst($action) ?> Cron Jobs</h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
	<p class="instructions">Below is the cron jobs (crontab) file that can be used to execute given tasks on a periodic basis. For example cron jobs are perfect
	for automatically backing up your database. In fact, we already have an example for you at <?=INSTALL_ROOT.'crons/crontab_default.php'?>. Below is an easy to use interface to manage the schedule of the tasks. 
	We recommend <a href="http://www.google.com/search?client=safari&rls=en-us&q=cron+job+tutorial&ie=UTF-8&oe=UTF-8" target="_blank"><strong>clicking here to learn more about cron jobs</strong></a> if
	you are not familiar with them already.</p>
	
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	<label for="mailto">Mail to:</label> <?=$this->form->text('mailto', $mailto, 'size="30"')?>
	<table border="0" cellspacing="0" cellpadding="0" class="cronjob">
		<tbody>
		
		<?php
		$newnum = count($cronjob_lines);
		foreach($cronjob_lines as $key => $line) { 
			if (substr(trim($line), 0, 1) == '#') continue;
			echo "<tr id=\"line".$key."\">\n";
			$time_fields = explode(' ', $line);
			$command = array();
			for ($i = 5; $i < count($time_fields); $i++)
			{
				$command[] = $time_fields[$i];
			}
			$command = implode(' ', $command);
			
			if (count($time_fields) > 5){
				echo "<td>".$this->form->text('min['.$key.']', $time_fields[0], 'size="7"')."</td>\n";
				echo "<td>".$this->form->text('hour['.$key.']', $time_fields[1], 'size="7"')."</td>\n";
				echo "<td>".$this->form->text('month_day['.$key.']', $time_fields[2], 'size="7"')."</td>\n";
				echo "<td>".$this->form->text('month_num['.$key.']', $time_fields[3], 'size="7"')."</td>\n";
				echo "<td>".$this->form->text('week_day['.$key.']', $time_fields[4], 'size="7"')."</td>\n";
				echo "<td>".$this->form->text('command['.$key.']', $command, 'size="60" style="float: left"')." <a href=\"#\" class=\"ico ico_remove_line\"></a></td>\n";
			}
			echo "</tr>\n";
		}
	 	?>
			<tr>
				<td><?=$this->form->text('min['.$newnum.']', 'min', 'class="fillin" size="7"')?></td>
				<td><?=$this->form->text('hour['.$newnum.']', 'hour', 'class="fillin" size="7"')?></td>
				<td><?=$this->form->text('month_day['.$newnum.']', 'month day', 'class="fillin" size="7"')?></td>
				<td><?=$this->form->text('month_num['.$newnum.']', 'month num', 'class="fillin" size="7"')?></td>
				<td><?=$this->form->text('week_day['.$newnum.']', 'week day', 'class="fillin" size="7"')?></td>
				<td><?=$this->form->text('command['.$newnum.']', 'command', 'class="fillin" size="60"')?></td>
			</tr>
		</tbody>
	</table>
	<div class="buttonbar">
		<ul>
			<li class="end"><a href="#" class="ico ico_no" id="remove">Remove Cron Job(s)</a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit">Save Cron Job(s)</a></li>
		</ul>
	</div>
	<?=$this->form->hidden('action', $action)?>
	<?=$this->form->close()?>
	</div>

</div>