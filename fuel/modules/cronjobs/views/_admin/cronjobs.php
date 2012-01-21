<?=js('CronjobsController', 'cronjobs')?>
<div id="fuel_main_content_inner">
	<p class="instructions"><?=lang('cronjobs_instructions')?></p>
	
	<?=$this->form->open(array('id' => 'form', 'method' => 'post'))?>
	<label for="mailto"><?=lang('cronjobs_mailto')?></label> <?=$this->form->text('mailto', $mailto, 'size="30"')?> &nbsp; 
	<?=lang('cronjobs_current_datetime').' '.date('Y-m-d H:i:s')?>
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
				<td><?=$this->form->text('min['.$newnum.']', '', 'placeholder="'.lang('cronjobs_min').'" size="7"')?></td>
				<td><?=$this->form->text('hour['.$newnum.']', '', 'placeholder="'.lang('cronjobs_hour').'" size="7"')?></td>
				<td><?=$this->form->text('month_day['.$newnum.']', '', 'placeholder="'.lang('cronjobs_month_day').'"  size="7"')?></td>
				<td><?=$this->form->text('month_num['.$newnum.']', '', 'placeholder="'.lang('cronjobs_month_num').'"  size="7"')?></td>
				<td><?=$this->form->text('week_day['.$newnum.']', '', 'placeholder="'.lang('cronjobs_week_day').'"  size="7"')?></td>
				<td><?=$this->form->text('command['.$newnum.']', '', 'placeholder="'.lang('cronjobs_command').'"  size="60"')?></td>
			</tr>
		</tbody>
	</table>
	<div class="buttonbar">
		<ul>
			<li class="end"><a href="#" class="ico ico_no" id="remove"><?=lang('btn_remove_cronjobs')?></a></li>
			<li class="end"><a href="#" class="ico ico_yes" id="submit"><?=lang('btn_save_cronjobs')?></a></li>
		</ul>
	</div>
	<?=$this->form->hidden('action', $action)?>
	<?=$this->form->close()?>
</div>
