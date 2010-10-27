	<?php 
	$screenshots = array();
	$screenshots['dashboard'] = 'Dashboard';
	$screenshots['inline_editing'] = 'Inline Editing';
	$screenshots['pages_tree'] = 'Tree View of Pages';
	$screenshots['page_create'] = 'Create Pages';
	$screenshots['assets'] = 'Asset Management';
	$screenshots['validate'] = 'Validate Links/HTML and Calculate Page Weight';
	$screenshots['validate_run'] = 'HTML Validation Results';
	$screenshots['google_rankings'] = 'Google Rankings';
	
	 ?>
	<div class="viewer" id="screenshots">
		<a href="#" class="viewer_arrow viewer_prev"></a>
		<div class="viewer_window">
			<div class="viewer_mask">
				<ul class="viewer_items">
				<?php foreach($screenshots as $key => $caption){ ?>
					<li><a href="<?=img_path('screenshots/big/screenshot_big_'.$key.'.jpg')?>" title="<?=$caption?>" class="zoom"><img src="<?=img_path('screenshots/sm/screenshot_sm_'.$key.'.jpg')?>" alt="<?=$caption?>" class="screenshot" /></a></li>
				<?php } ?>
				</ul>
			</div>
		</div>
		<a href="#" class="viewer_arrow viewer_next"></a>
		<ul class="viewer_nav">
		</ul>
	</div>