<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Error</title>
<style>
html,body { margin: 20px 0 10px 0; padding: 0; }
body { font: 11px Arial, Helvetica, sans-serif; text-align: center; }
h1 { margin: 0; padding:0; font-family: Arial,Helvetica,sans-serif; font-size: 22px; color: #999; font-weight: normal; padding: 10px 4px 5px 0; }
p { font-size: 12px; margin: 0 0 10px 0; line-height: 15px; }
a { color: #690; text-decoration: none; }
a:hover { color: #333; text-decoration: underline; }
#error_general { width: 500px; margin: auto; border: 1px solid #ddd; padding: 0 20px 20px 20px;}
</style>
</head>
<body>
	<div id="error_general">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</body>
</html>