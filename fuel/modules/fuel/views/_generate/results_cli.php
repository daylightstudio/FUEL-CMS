<?php

echo 'CREATED';
if ( ! empty($created))
{
	foreach($created as $val) echo '* '.$val;
}
else
{
	echo 'There were no files created.';
}

if ( ! empty($modifed))
{
	echo 'MODIFIED';
	foreach ($modified as $val) echo '* '.$val;
}

if ( ! empty($errors))
{
	echo 'ERRORS';
	foreach ($errors as $val) echo '* '.$val;
}