<?php
$sentence['url'] = $this->Html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentence['id']
));
echo json_encode($sentence);
?>