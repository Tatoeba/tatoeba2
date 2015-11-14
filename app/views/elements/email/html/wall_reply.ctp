<?php
echo "<p>";
echo $html->link($linkToMessage, $linkToMessage);
echo "</p>";

echo $html->div(null, $messages->formatedContent($messageContent),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);
?>