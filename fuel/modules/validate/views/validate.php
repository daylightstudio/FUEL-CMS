<?=js('ValidateController', 'validate')?>
<div id="main_top_panel">
	<h2 class="ico ico_tools_validate"><a href="<?=fuel_url('tools')?>">Tools</a> &gt; Validate</h2>
</div>
<div class="clear"></div>

<div id="notification" class="notification">
	<?=$notifications?>
</div>
<div id="main_content" class="noaction">

	<div id="main_content_inner">
		<p class="instructions">Select the pages on the left to validate. Then select whether you want to validate the HTML or the page links for each page. 
		Processing time may take seconds to several minutes depending on the number of pages selected.
		For HTML validation, it is recommended that you either <a href="http://developer.apple.com/internet/opensource/validator.html" target="blank"><strong>setup a local validation server</strong></a>, 
		or validate only several at a time to avoid being temporarily blocked from the w3c.org.
		</p>
		<form action="<?=fuel_url('tools/validate/html')?>" method="post" id="form">
			<?=$this->form->select('pages[]', $pages_select, $this->input->post('pages'), array('multiple' => 'multiple'))?>
			<div class="clear"></div>
			
			<?=$this->form->textarea('pages_input', (!empty($default_page_input) ? $default_page_input : lang('pages_input')), 'cols="5" rows="100" class="fillin"')?>
			
			<div style="text-align: center; margin: 0 0 30px 135px;" class="buttonbar">
				<ul>
					<li class="end"><a href="#" class="ico ico_validate_links" id="submit_links">Validate Links</a></li>
					<li class="end"><a href="#" class="ico ico_tools_validate" id="submit_html">Validate HTML</a></li>
					<li class="end"><a href="#" class="ico ico_validate_size" id="submit_size_report">View Size Report</a></li>
				</ul>
			</div>
		</form>
	</div>
	
</div>