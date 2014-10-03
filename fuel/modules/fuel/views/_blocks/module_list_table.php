<?php

echo
$table.
$this->form->hidden('offset', $params['offset']).
$this->form->hidden('order', $params['order']).
$this->form->hidden('col', $params['col']);

if ( ! empty($params['precedence'])) echo $this->form->hidden('precedence', $params['precedence']);