<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
?>
<div id="<?= h($key) ?>Message" class="<?= h($class) ?>">
  <?= $message ?>
</div>
