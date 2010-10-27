<?php foreach($blocks as $block){ ?>
<div id="blog_<?=$block?>" class="blog_block">
	<?=$this->fuel_blog->block($block)?>
</div>
<?php } ?>