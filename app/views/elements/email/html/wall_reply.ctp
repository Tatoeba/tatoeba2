<?php
echo $html->tag("p", $linkToMessage);

echo $html->div(null, $messages->formatedContent($messageContent),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);
?>