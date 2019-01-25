<?php
$urlParams = array(
    'controller' => 'wall',
    'action' => 'show_message',
    $postId,
    '#' => 'message_'.$postId
);

$url = $this->Url->build($urlParams, true);
echo "<p>";
echo $this->Html->link($url);
echo "</p>";

echo $this->Html->div(null, $this->Messages->formatedContent($messageContent),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);
?>