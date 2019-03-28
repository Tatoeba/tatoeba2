<?php

foreach ($list as $item) {
    $this->Sentences->displayGenericSentence(
        $item->sentence, 'mainSentence', false
    );
}

$this->Pagination->display();
