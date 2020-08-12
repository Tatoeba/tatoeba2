<?php
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>
<md-button class="md-primary lang-button" ng-href="<?= $baseUrl ?>/{{lang.code}}/none">
  <div class="lang-name">
    <svg class="language-icon">
        <use ng-attr-xlink:href="{{getFlagSpriteUrl(lang.code)}}"
             xlink:href="" />
    </svg>
    <strong>{{lang.name}}</strong>
  </div>
  <small>{{lang.sentences}}</small>
</md-button>
