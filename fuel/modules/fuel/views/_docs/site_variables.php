<h1>Site Variables</h1>
<p>Site Variables are global variables you can use throughout your site. The <dfn>scope</dfn> property can be a regular expression 
(similar to variables when using <a href="<?=user_guide_url('general/opt-in-controllers')?>">opt-in controllers</a>) or left empty for it
to apply for the entire site.
Use the <a href="<?=user_guide_url('helpers/fuel_helper')?>">fuel_var</a> function in your views to merge in a global variable 
or ({fuel_var('varname')} in a form field that doesn't allow PHP).
</p>

<img src="<?=img_path('examples/screen_sitevariables.jpg', 'user_guide')?>" class="screen" />