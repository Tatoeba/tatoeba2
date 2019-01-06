<?php
$urlParams = array(
    'controller' => 'wall',
    'action' => 'show_message',
    $postId,
    '#' => 'message_'.$postId
);

echo "<p>";
echo $this->Url->build($urlParams, true);
echo "</p>";

echo $this->Html->div(null, $this->Messages->formatedContent($messageContent),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);
?>