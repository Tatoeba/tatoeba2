<?php
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>

<?php foreach ($stats as $milestone => $languages): ?>
  <h3 class="header-with-hline">
    <span class="hline">
      <span class="text">
        <?= h(format(
                __n("{n}+ sentence", "{n}+ sentences", $milestone),
                ['n' => $this->Number->format($milestone)]
            )) ?>
      </span>
    </span>
  </h3>
  <div class="language-list">
    <ul>
      <?php
        $l = h(json_encode($languages));
      ?>

      <li ng-repeat="lang in <?= $l ?>">
        <a ng-href="<?= $baseUrl ?>/{{lang.code}}/none">
          <svg class="language-icon">
              <use ng-attr-xlink:href="{{getFlagSpriteUrl(lang.code)}}"
                   xlink:href="" />
          </svg>
          <span class="lang-name">{{lang.name}}</span>
        </a>
      </li>
    </ul>
  </div>
<?php endforeach; ?>
