<?php
$this->assign('title', 'Tatoeba API');

$docurl = [
    'version' => 'v1',
    'controller' => 'doc',
    'action' => 'show',
];
?>

<div style="padding: 20px">
  <p>
    Welcome to the Tatoeba API.
  </p>
  <p>
    You can read the <?= $this->Html->link('documentation', $docurl) ?>.
  </p>
</div>
