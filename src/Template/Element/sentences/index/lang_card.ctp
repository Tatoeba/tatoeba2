<?php
  $code = $code ?? 'unknown';
  $url = $this->Url->build(['action' => 'show_all_in', $code, 'none']);
?>
<md-button class="md-primary lang-button" href="<?= h($url) ?>">
  <div class="lang-name">
    <svg class="language-icon">
        <use xlink:href="/cache_svg/allflags.svg#<?= $code ?>" />
    </svg>
    <strong><?= h($name) ?></strong>
  </div>
  <small><?= h($sentences) ?></small>
</md-button>
