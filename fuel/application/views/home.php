<?php fuel_set_var('layout', '')?>
<?php if ($CI->config->item('fuel_mode', 'fuel') == 'views') : ?>

<?php $CI->load->view('install') ?>

<?php else : ?>

<?php $this->load->view('_blocks/header')?>

<div id="bigpic">
	<img src="<?php echo img_path('home_pic.jpg')?>" />
</div>

<div id="home_cols">
	
	<div id="home_news">
		<?php $posts = fuel_model('blog_posts', array('find' => 'all', 'limit' => 3, 'order' => 'sticky, date_added desc', 'module' => 'blog')) ?>
		<?php if (!empty($posts)) : ?>
		<h2>The Latest from our Blog</h2>
		<ul>
		<?php foreach($posts as $post) : ?>
		<li>
			<h4><a href="<?php echo $post->url; ?>"><?php echo $post->title; ?></a></h4>
			<?php echo $post->get_excerpt_formatted(200, 'Read More'); ?>
			</li>
		<?php endforeach; ?>
		</ul>
		
		<?php endif; ?>
	</div>
	
	<div id="home_about">
		<h2>WidgiCorp </h2>
		<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla tincidunt condimentum nulla. Sed ut elit. Morbi lectus. Sed iaculis lacus eget elit. In hac habitasse platea dictumst. Nullam semper semper risus.</p>
		<ul>
			<li>Lorem ipsum dolor sit amet</li>
			<li>Consectetuer adipiscing elit</li>
			<li>Sed ut elit. Morbi lectus. Sed</li>
		</ul>
	</div>
	
	<div class="clear"></div>
</div>

<?php $this->load->view('_blocks/footer')?>
<?php endif; ?>