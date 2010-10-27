<?php header("HTTP/1.1 404 Not Found"); ?>
<?php 
define('USE_FUEL_MARKERS', FALSE);
include(APPPATH.'views/_variables/global.php');
$GLOBALS['page_title'] = '404 Error : Page Cannot Be Found';
extract($vars);

require_once(APPPATH.'helpers/asset_helper.php');
require_once(APPPATH.'helpers/MY_html_helper.php');
require_once(APPPATH.'helpers/MY_url_helper.php');
require_once(APPPATH.'helpers/my_helper.php');
include(APPPATH.'views/_blocks/header.php');
?>	
</div>

<div id="main">
	<div id="content" style="margin: -70px 0 0 320px;">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</div>

<?php include(APPPATH.'views/_blocks/footer.php'); ?>