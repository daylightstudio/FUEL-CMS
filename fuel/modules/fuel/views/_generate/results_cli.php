CREATED 
<?php if (!empty($created)) : ?>
<?php foreach($created as $val) : ?>
* <?=$val?> 
<?php endforeach; ?>
<?php else: ?>
There were no files created.
<?php endif; ?>

<?php if (!empty($modifed)) : ?>
MODIFIED 
<?php foreach($modified as $val) : ?>
* <?=$val?> 
<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
ERRORS 
<?php foreach($errors as $val) : ?>
* <?=$val?> 
<?php endforeach; ?>
<?php endif; ?>