<?php
$sentence['url'] = $this->Url->build(array(
    'controller' => 'sentences',
    'action' => 'show',
    $sentence['id']
));
echo json_encode($sentence);
?>