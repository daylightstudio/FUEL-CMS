<?php $this->load->view('_blocks/header')?>

<div id="main">
	<div id="main_inner">
		
		<div id="main_inner_top"></div>
		
		<div id="left">
			<?=$this->fuel_blog->sidemenu(array('search', 'categories'))?>
		</div>

		<div id="right">
			<div id="content">
				<?=$body?>
			</div>
		</div>
		
		<div class="clear"></div>
		
	</div>
</div>

<?php $this->load->view('_blocks/footer')?>