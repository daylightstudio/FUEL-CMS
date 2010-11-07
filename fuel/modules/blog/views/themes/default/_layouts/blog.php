<?php $this->load->view('_blocks/header')?>
	
	<div id="right">
		<?php echo $this->fuel_blog->sidemenu(array('search', 'authors', 'categories', 'links', 'archives'))?>
	</div>

	<div id="main_inner">
		<?php echo fuel_var('body', ''); ?>
	</div>
	
	<div class="clear"></div>
	
<?php $this->load->view('_blocks/footer')?>
