<?php
$urlParams = [
    'lang' => '',
    'controller' => 'wall',
    'action' => 'show_message',
    $post->id,
    '#' => 'message_'.$post->id,
];

$url = $this->Url->build($urlParams, true);
echo "<p>";
echo $this->Html->link($url);
echo "</p>";

echo $this->Html->div(
    null,
    $this->Messages->formatContent($post->content),
    [
        'style' => 'background:#f1f1f1;padding:20px',
    ]
);
?>