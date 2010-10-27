<?php $archives_by_month = $CI->fuel_blog->get_post_archives(); ?>
<?php if (!empty($archives_by_month)) : ?>
<div class="blog_block">
	<h3>Archives</h3>
	<ul>
		<?php foreach($archives_by_month as $month => $archives) : 
			$month_str = date('F Y', strtotime(str_replace('/', '-', $month).'-01'));
			?>
		<li>
			<a href="<?=$this->fuel_blog->url($month)?>"><?=$month_str?></a> (<?=count($archives)?>)
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>
