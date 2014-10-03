<?php

if ($this->fuel->auth->has_permission('logs'))
{
	if ( ! empty($latest_activity))
	{
		echo '
		<div class="dashboard_pod" style="width: 400px;">
			<h3>'.lang('dashboard_hdr_latest_activity').'</h3>
			<ul class="nobullets">';

		foreach ($latest_activity as $val)
		{
			echo '<li><strong>'.english_date($val['entry_date'], true).':</strong> '.$val['message'].' - '.$val['name'].'</li>';
		}

		echo '
			</ul>
			<a href="'.fuel_url('logs').'">'.lang('dashboard_view_all_activity').'</a>
		</div>';
	}
}

if ( ! empty($feed))
{
	echo '
	<div class="dashboard_pod" style="width: 230px;">
		<h3>'.lang('dashboard_hdr_latest_news').'</h3>';

	if (isset($latest_fuel_version) && ! empty($latest_fuel_version))
	{
		echo '
			<div class="update_notice">
				<a href="http://www.getfuelcms.com" target="_blank">FUEL CMS '.$latest_fuel_version.'</a> is available!<br />
				You are on version <em>'.FUEL_VERSION.'</em><br />
				Please update now.
			</div>';
	}

	echo '<ul class="nobullets">';

	foreach ($feed as $item)
	{
		echo '<li><a href="'.$item->get_link(0).'" target="_blank">'.$item->get_title().'</a></li>';
	}

	echo '
		</ul>
		<a href="'.$this->config->item('dashboard_rss', 'fuel').'">'.lang('dashboard_subscribe_rss').'</a>
	</div>';
}

if ( ! empty($recently_modifed_pages))
{
	echo '
	<div class="dashboard_pod" style="width: 230px;">
		<h3>'.lang('dashboard_hdr_modified').'</h3>
		<ul class="nobullets">';

	foreach ($recently_modifed_pages as $val)
	{
		echo '<li><a href="'.fuel_url('pages/edit/'.$val['id']).'">'.$val['location'].'</a></li>';
	}

	echo '
		</ul>
		<a href="'.fuel_url('pages').'">'.lang('dashboard_view_all_pages').'</a>
	</div>';
}

if ( ! empty($docs) && $this->fuel->auth->has_permission('site_docs'))
{
	echo '
	<div class="dashboard_pod" style="width: 230px;">
		<h3>'.lang('dashboard_hdr_site_docs').'</h3>';

	if (is_array($docs))
	{
		echo '<ul class="nobullets">';

		foreach ($docs as $url => $title)
		{
			echo '<li><a href="'.$url.'" target="_blank">'.$title.'</a></li>';
		}

		echo '</ul>';
	}
	else
	{
		echo $docs;
	}

	echo '</div>';
}

echo '<div class="clear"></div>';