<?php
  $baseUrl = $this->Url->build(['action' => 'show_all_in']);
?>

<div id="language-search">
  <?= $this->element(
          'language_dropdown',
          array(
              'name' => 'ShowAllIn',
              'languages' => $this->Languages->unknownLanguagesArray(false),
              /* @translators: placeholder text in search field of
                               Browse by languages page */
              'placeholder' => __('Search a language'),
              'onSelectedLanguageChange' => 'vm.onSelectedLanguageChange(language)',
          )
      );
  ?>
  <md-button class="md-raised md-primary"
             ng-cloak
             ng-if="vm.showAllSentencesButtonText"
             ng-href="<?= $baseUrl ?>/{{vm.selectedLanguage.code}}/none">
    {{vm.showAllSentencesButtonText}}
  </md-button>
</div>
