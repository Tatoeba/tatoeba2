<?php
$sentence['url'] = $html->url(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentence['id']
));
echo json_encode($sentence);
?>