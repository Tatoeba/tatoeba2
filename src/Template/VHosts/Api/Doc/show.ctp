<?php
use Cake\Routing\Router;
$this->Html->script('/js/openapi-explorer.min.js', ['block' => 'script', 'type' => 'module']);
$this->Html->css('/css/openapi-explorer.css', ['block' => 'css']);
$this->assign('title', 'Tatoeba API');
$this->assign('navlinks', '<li>' . $this->Html->Link('OpenAPI file', $specurl) . '</li>');
?>
<openapi-explorer
   spec-url="<?= h($specurl) ?>"
   show-authentication="false"
   server-url="<?= h(Router::fullBaseUrl()) ?>">
</openapi-explorer>
