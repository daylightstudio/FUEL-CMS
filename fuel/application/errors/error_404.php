<?php header("HTTP/1.1 404 Not Found"); ?>
<?php 
define('USE_FUEL_MARKERS', FALSE);
include(APPPATH.'views/_variables/global.php');
extract($vars);

// set the 404 page title
$GLOBALS['page_title'] = '404 Error : Page Cannot Be Found';

// to prevent weird CSS errors if someone passes a name of a class used in your CSS
$GLOBALS['body_class'] = '';

require_once(APPPATH.'helpers/asset_helper.php');
require_once(APPPATH.'helpers/MY_html_helper.php');
require_once(APPPATH.'helpers/MY_url_helper.php');
require_once(APPPATH.'helpers/my_helper.php');
include(APPPATH.'views/_blocks/header.php');
?>	

<div id="error_404">
	<h1><?php echo $heading; ?></h1>
	<?php echo $message; ?>
</div>

<?php include(APPPATH.'views/_blocks/footer.php'); ?>