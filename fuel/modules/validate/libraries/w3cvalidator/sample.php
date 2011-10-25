<?php
set_time_limit(3600);
include("CurlObj.class.php");
include("W3cValidator.class.php");
$check = new W3cValidator();
$url = 'http://www.nasa.gov/';
$result = $check->validate($url);
echo '<pre><xmp>';
var_dump($result);
echo '</xmp></pre>';
?>
<?php
$output = $check->get_curl_output();
echo '<pre><xmp>';
var_dump($output);
echo '</xmp></pre>';
?>