<?=$table?>
<?=$this->form->hidden('offset', $params['offset'])?>
<?=$this->form->hidden('order', $params['order'])?>
<?=$this->form->hidden('col', $params['col'])?>
<?php if (!empty($params['precedence'])) : ?>
<?=$this->form->hidden('precedence', $params['precedence'])?>
<?php endif; ?>