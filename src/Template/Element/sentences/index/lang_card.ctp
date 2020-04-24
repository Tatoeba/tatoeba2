<?php
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>
<md-button class="md-primary lang-button" ng-href="<?= $baseUrl ?>/{{lang.code}}/none">
  <div class="lang-name">
    <img class="language-icon" width="30" height="20"
         ng-attr-title="{{lang.name ? lang.name : lang.code}}"
         ng-src="/img/flags/{{lang.code}}.svg" />
    <strong>{{lang.name}}</strong>
  </div>
  <small>{{lang.sentences}}</small>
</md-button>
