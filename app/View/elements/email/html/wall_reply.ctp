<?php
echo "<p>";
echo $this->Html->link($linkToMessage, $linkToMessage);
echo "</p>";

echo $this->Html->div(null, $this->Messages->formatedContent($messageContent),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);
?>