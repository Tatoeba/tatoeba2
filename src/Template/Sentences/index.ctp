<?php
$title = __('Language index');
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>
<div id="content-container">
<section>

<?php
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>
<div ng-cloak>
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
            <img class="language-icon" width="30" height="20"
                 ng-attr-title="{{lang.name ? lang.name : lang.code}}"
                 ng-src="/img/flags/{{lang.code}}.svg" />
            <span class="lang-name">{{lang.name}}</span>
        </a>
        </li>
      </ul>
    </div>
  <?php endforeach; ?>
</div>

</section>
</div>
