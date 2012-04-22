<?=$table?>
<?php /* DO NOT ADD BECAUSE IT WILL MESS WITH THE PAGINATION... LEFT HERE AS A REMINDER ?>
<?=$this->form->hidden('offset', $params['offset'])?>
<?php */ ?>
<?=$this->form->hidden('order', $params['order'])?>
<?=$this->form->hidden('col', $params['col'])?>
<?php if (!empty($params['precedence'])) : ?>
<?=$this->form->hidden('precedence', $params['precedence'])?>
<?php endif; ?>