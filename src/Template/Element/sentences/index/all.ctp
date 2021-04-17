<?php
foreach ($stats as $milestone => $languages):
  if ($milestone == 0) {
    /* @translators: section header in Browse by language page */
    $header = __('0 sentences');
  } elseif ($milestone == 1) {
    /* @translators: section header in Browse by language page */
    $header = __('1 or more sentences');
  } else {
    $header = format(
      /* @translators: section header in Browse by language page */
      __n("{n}+ sentence", "{n}+ sentences", $milestone),
      ['n' => $this->Number->format($milestone)]
    );
  }
?>
  <h3 class="header-with-hline">
    <span class="hline">
      <span class="text">
        <?= h($header) ?>
      </span>
    </span>
  </h3>
  <div class="language-list">
    <ul>
      <?php
        foreach ($languages as $lang) {
          $code = $lang->code ?? 'unknown';
          $url = $this->Url->build(['action' => 'show_all_in', $code, 'none']);
          ?>
            <li>
              <a href="<?= h($url) ?>">
                <svg class="language-icon">
                  <use xlink:href="/cache_svg/allflags.svg#<?= $code ?>" />
                </svg>
                <span class="lang-name"><?= h($lang->name) ?></span>
              </a>
            </li>
      <?php
        }
      ?>
    </ul>
  </div>
<?php endforeach; ?>
