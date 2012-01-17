<div id="fuel_main_content_inner">
	<div id="search_index_results"><div class="loader"></div></div>
	
	<script type="text/javascript">
	//<![CDATA[
		$(function(){
			$.get('<?=fuel_url('tools/search/index_site')?>', function(html){
				$('#search_index_results').html(html);
			});
		})
	//]]>
	</script>
</div>