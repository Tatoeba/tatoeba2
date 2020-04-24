<div id="logo-container">
  <img ng-cloak class="centered-logo" src="/img/tatoeba.svg" width="200px" />
</div>

<div ng-cloak class="languages-around-logo">
  <div class="language-around-logo lang-card"
       ng-class="'lang'+($index+1)"
       ng-repeat="lang in <?= $top10 ?>">
    <?= $this->element('sentences/index/lang_card'); ?>
  </div>
</div>
