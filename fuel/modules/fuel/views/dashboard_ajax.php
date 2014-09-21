<?php if ($this->fuel->auth->has_permission('logs')) : ?>
<?php if (!empty($latest_activity)) : ?>
<div class="dashboard_pod" style="width: 400px;">

	<h3><?=lang('dashboard_hdr_latest_activity')?></h3>
	<ul class="nobullets">
		<?php foreach($latest_activity as $val) : ?>
		<li><strong><?=english_date($val['entry_date'], true)?>:</strong> <?=$val['message']?> - <?=$val['name']?></li>
		<?php endforeach; ?>
	</ul>
	<a href="<?=fuel_url('logs')?>"><?=lang('dashboard_view_all_activity')?></a>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($feed)) : ?>
<div class="dashboard_pod" style="width: 230px;">

	<h3><?=lang('dashboard_hdr_latest_news')?></h3>
	
	<?php if (isset($latest_fuel_version) AND ! empty($latest_fuel_version)) : ?>
		<div class="update_notice">
			<a href="http://www.getfuelcms.com" target="_blank">FUEL CMS <?php echo $latest_fuel_version; ?></a> is available!<br />
			You are on version <em><?php echo FUEL_VERSION; ?></em><br />
			Please update now.
		</div>
	<?php endif; ?>
	
	<ul class="nobullets">
		<?php foreach($feed as $item) : ?>
		<li><a href="<?=$item->get_link(0)?>" target="_blank"><?=$item->get_title()?></a></li>
		<?php endforeach; ?>
	</ul>
	<a href="<?=$this->config->item('dashboard_rss', 'fuel')?>"><?=lang('dashboard_subscribe_rss')?></a>
</div>
<?php endif; ?>

<?php if (!empty($recently_modifed_pages)) : ?>
<div class="dashboard_pod" style="width: 230px;">
	<h3><?=lang('dashboard_hdr_modified')?></h3>
		<ul class="nobullets">
			<?php foreach($recently_modifed_pages as $val) : ?>
			<li><a href="<?=fuel_url('pages/edit/'.$val['id'])?>"><?=$val['location']?></a></li>
			<?php endforeach; ?>
		</ul>
		<a href="<?=fuel_url('pages')?>"><?=lang('dashboard_view_all_pages')?></a>
</div>
<?php endif; ?>


<?php if (!empty($docs) AND $this->fuel->auth->has_permission('site_docs')) : ?>
<div class="dashboard_pod" style="width: 230px;">

	<h3><?=lang('dashboard_hdr_site_docs')?></h3>
	<?php if (is_array($docs)) : ?>
	<ul class="nobullets">
		<?php foreach($docs as $url => $title) : ?>
		<li><a href="<?=$url?>" target="_blank"><?=$title?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php else: ?>
	<?=$docs?>
	<?php endif; ?>
</div>
<?php endif; ?>



<div class="clear"></div>
