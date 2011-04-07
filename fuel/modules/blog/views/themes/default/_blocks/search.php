<div class="blog_search">
	<form method="post" action="<?=$this->fuel_blog->url('search')?>">
		<input type="text" name="q" value="" id="q" class="fillin_input" />
		<input type="submit" value="Search" class="search_btn" />
		
		<!-- dummy value. Used so that we can get query strings to work if form method equals GET (which by defualt it is not)... needs more then one query string param to work -->
		<input type="hidden" name="x" value="" /> 
	</form>
</div>