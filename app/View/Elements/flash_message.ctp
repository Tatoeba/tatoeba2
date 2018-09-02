<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
?>
<div id="<?= h($key) ?>Message" class="<?= h($class) ?>">
  <?= $message ?>
  <? if (isset($params['errors'])) echo $this->Html->nestedList($params['errors']); ?>
</div>
