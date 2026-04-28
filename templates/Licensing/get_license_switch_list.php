<?php

$this->Paginator->options(['url' => [ 'action' => 'switch_my_sentences' ]]);
echo $this->element('licensing/list', compact($list));
