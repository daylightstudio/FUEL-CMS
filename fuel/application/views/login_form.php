<div class="login_form">
<?php
$userData = $this->session->userdata('userData');
if($userData)
{
	echo 'Hey-o, <a href="http://www.wetjacket.com/'. $userData['user_name'].'" target="_blank">' . $userData['user_name'] . '</a>'; ?>
	<?=form_open(base_url() . 'login')?>
	<?=form_hidden('redirect',current_url());?>
	<?=form_submit('','Logout')?>
	<?=form_close();?>
	<?php
}
else {?>


<?=form_open(base_url() . 'login')?>
	<?=form_hidden('redirect', current_url());?>
	<?=form_input('userID', set_value('userID', 'Email or Username'))?>
	<?=form_error('userID')?>
	<?=form_password('userPassword',set_value('userPassword','password'))?>
	<?=form_error('userPassword')?>
	<?=form_submit('', 'Login')?><br>
	<p><a href="http://www.wetjacket.com/user/register/" rel="nofollow">Sign Up</a> | <a href="http://www.wetjacket.com/user/password/request/" rel="nofollow">Forgot Password</a></p>
<?=form_close()?>
<?php
}
?>
</div>
