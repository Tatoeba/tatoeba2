<?php

echo json_encode(format(
    /* @translators: button appearing after a language search
                     in Browse by languages page. */
    __('Show all sentences in {language}'),
    ['language' => $this->Languages->codeToNameToFormat($code)]
));
