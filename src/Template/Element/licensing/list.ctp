<?php

$title = $this->Paginator->counter(
    __('List of affected sentences (total {{count}})')
);

echo $this->Html->tag('h3', $title);

foreach ($list as $item) {
    $this->Sentences->displayGenericSentence(
        $item->sentence, 'mainSentence', false
    );
}

$this->Pagination->display();
