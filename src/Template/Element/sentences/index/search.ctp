<?php
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>

<div id="language-search">
  <?= $this->element(
          'language_dropdown',
          array(
              'name' => 'ShowAllIn',
              'languages' => $this->Languages->unknownLanguagesArray(false),
              'placeholder' => __('Search a language'),
              'openOnFocus' => false,
          )
      );
  ?>
  <md-button class="md-raised md-primary"
             ng-cloak
             ng-if="vm.selectedLanguage"
             ng-href="<?= $baseUrl ?>/{{vm.selectedLanguage.code}}/none">
    <?= format(
            __('Show all sentences in {language}'),
            ['language' => '{{vm.selectedLanguage.name}}']
        ) ?>
  </md-button>
</div>
