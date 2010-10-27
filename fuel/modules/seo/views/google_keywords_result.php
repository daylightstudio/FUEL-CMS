<?=js('SeoController', 'seo')?>
<?php /* ?>
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script language="Javascript" type="text/javascript">
//<![CDATA[
google.load('search', '1');

function OnLoad() {
  // Create a search control
  var searchControl = new google.search.SearchControl();

  // Add in a full set of searchers
  var localSearch = new google.search.LocalSearch();
  searchControl.addSearcher(localSearch);
  searchControl.addSearcher(new google.search.WebSearch());

  // Set the Local Search center point
  localSearch.setCenterPoint("<?=$centerpoint?>");

  // tell the searcher to draw itself and tell it where to attach
  searchControl.draw(document.getElementById("searchcontrol"));

  // execute an inital search
  searchControl.execute("<?=implode(',', $results) ?>");
}
google.setOnLoadCallback(OnLoad);

//]]>
</script>

<div id="searchcontrol">Loading</div>
<?php */ ?>
<?php if (!empty($results)){ ?>
	<h3>Google rankings per keyword out of top <?=$num_results?></h3>
	<ul class="nobullets">
	<?php foreach($results as $keyword => $found){ ?>
			<li><a href="http://www.google.com/search?q=<?=rawurlencode($keyword)?>&num=<?=$this->config->item('keyword_google_num_results', FUEL_FOLDER)?>" target="_blank"><strong class="success"><?=$keyword?></strong></a> 
				ranking<?php if (count($found) != 1) {?>s<?php } ?>: <?=implode(', ', $found) ?>
			</li>
	<?php } ?>
	</ul>
<?php } else {?>
	<span class="warning">The domain <strong><?=$domain?></strong> was not found in the top <?=$num_results?> Google results using the search keywords <strong><?=$keywords?></strong>.</span>
<?php } ?>