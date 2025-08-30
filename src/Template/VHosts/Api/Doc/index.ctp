<?php
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
  <p>
    API usage examples in JavaScript:
  </p>
  <ul>
    <li><?= $this->Html->link('Search for sentences by keywords', ['action' => 'examples', 'name' => 'sentences_search']) ?></p>
    <li><?= $this->Html->link('Download sentences as text', ['action' => 'examples', 'name' => 'text_download']) ?></p>
  </ul>
</div>
