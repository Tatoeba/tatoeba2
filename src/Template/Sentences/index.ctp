<?php
$title = __('Language index');
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>
<div id="content-container">
<section>

<?php
  $n = count($stats, COUNT_RECURSIVE);
?>
<h1>
  <div>
    <?= format(
          __n(
               'There is {n} language on Tatoeba',
               'There are {n} languages on Tatoeba',
               $n
          ),
          compact('n')
    ) ?>
  </div>
  <img ng-cloak class="centered-logo" src="/img/tatoeba.svg" width="200px" />
</h1>

<?php
  $top10 = $this->ShowAll->computeTopTen($stats);
  $top10 = h(json_encode($top10));
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>

<div ng-cloak class="languages-around-logo">
  <div class="language-around-logo" ng-class="'lang'+($index+1)" ng-repeat="lang in <?= $top10 ?>">
    <md-button class="md-primary lang-card" ng-href="<?= $baseUrl ?>/{{lang.code}}/none">
      <div class="lang-name">
        <img class="language-icon" width="30" height="20"
             ng-attr-title="{{lang.name ? lang.name : lang.code}}"
             ng-src="/img/flags/{{lang.code}}.svg" />
        <strong>{{lang.name}}</strong>
      </div>
      <small>{{lang.sentences}}+ sentences</small>
    </md-button>
  </div>
</div>

<h2 ng-cloak class="header-with-hline">
  <span class="hline">
    <span class="text">
      <a ng-click="allShowed = !allShowed">
        <?= format(
              __('Show all languages {arrowIcon}'),
              ['arrowIcon' =>
                 '<md-icon ng-if="!allShowed">keyboard_arrow_down</md-icon>'.
                 '<md-icon ng-if="allShowed" >keyboard_arrow_up</md-icon>'
              ]
        ) ?>
      </a>
    </span>
  </span>
</h2>

<div ng-cloak ng-show="allShowed">
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
