<?php

echo '
<div id="fuel_main_top_panel">
	<h2 class="ico '.$titlebar_icon.'">';

	if ( ! empty($titlebar))
	{
		if (is_array($titlebar))
		{
			$last_key = array_pop($titlebar);

			foreach ($titlebar as $url => $crumb)
			{
				if ( ! $this->fuel->admin->is_inline()) echo '<a href="'.fuel_url($url).'">';

				echo $crumb;

				if ( ! $this->fuel->admin->is_inline()) echo '</a>';

				echo "&gt;";
			}

			echo $last_key;
		}
		else
		{
			echo $titlebar;
		}
	}

	echo '</h2>';

	if ( ! $this->fuel->admin->is_inline() && ! empty($user))
	{
		echo '
		<div id="fuel_login_logout">'.
			lang('logged_in_as').'
			<a href="'.fuel_url('my_profile/edit/').'"><strong>'.$user['user_name'].'</strong></a>';

			if ($this->session->userdata('original_user_id') && $this->session->userdata('original_user_hash'))
			{
				echo
				"&nbsp;&nbsp;|&nbsp;&nbsp;".'
				<a href="'.fuel_url('users/login_as/'.$this->session->userdata('original_user_id').'/'.$this->session->userdata('original_user_hash')).'">'.lang('logout_restore_original_user').'</a>';
			}

			echo "
			&nbsp;&nbsp;|&nbsp;&nbsp;".'
			<a href="'.fuel_url('logout').'">'.lang('logout').'</a>
		</div>';
	}

echo '
</div>
<div class="clear"></div>';