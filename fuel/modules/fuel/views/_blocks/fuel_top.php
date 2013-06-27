<div id="fuel_top">
	<div id="nav_toggle">Menu</div>
	<script>
	$(function() {
		$('#nav_toggle').on('click', function(event) {
			event.preventDefault();
			$('html').toggleClass('nav_show');
		});
	});
	</script>
	<h1 id="fuel_site_name"><a href="<?=site_url()?>"><?=$this->fuel->config('site_name')?></a></h1>
</div>