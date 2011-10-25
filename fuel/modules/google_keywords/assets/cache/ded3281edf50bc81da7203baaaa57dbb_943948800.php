<?php
ob_start();
ob_start ("ob_gzhandler");

?>.ico_tools_seo_google_keywords{background-image:url(../images/ico_magnifier.png);}.ico_tools_seo{background-image:url(../images/ico_page_find.png);}<?php
ob_end_flush();
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 3600;
$exp = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
header($exp);
$size = "Content-Length: ".ob_get_length();
header($size);
ob_end_flush();
?>
