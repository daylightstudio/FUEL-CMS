<?php 
/*
Example block that displays a list month/year of posts for a particular module
*/
$archives_by_month = $CI->fuel->posts->get_post_archives(); ?>
<?php if (!empty($archives_by_month)) : ?>
<div class="post_archives">
	<select name="archives" id="archives" class="go">
		<option>Archives</option>
		<?php foreach($archives_by_month as $month => $archives) : 
			$month_str = date('F Y', strtotime(str_replace('/', '-', $month).'-01'));
			?>
		<option value="<?=$this->fuel->posts->url('archive/'.$month)?>"><?=$month_str?></a> (<?=count($archives)?>)</option>
		<?php endforeach; ?>
	</select>
</div>
<?php endif; ?>
