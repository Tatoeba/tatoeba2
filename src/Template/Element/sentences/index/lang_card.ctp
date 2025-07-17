<?php
  $code = $code ?? 'unknown';
  $url = $this->Url->build(['action' => 'show_all_in', $code, 'none']);
?>
<md-button class="md-primary lang-button" href="<?= h($url) ?>">
  <div class="lang-name">
    <?= $this->Languages->spriteIcon($code) ?>
    <strong><?= h($name) ?></strong>
  </div>
  <small><?= h($sentences) ?></small>
</md-button>
