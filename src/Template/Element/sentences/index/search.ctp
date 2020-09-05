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
              'openOnFocus' => false,
          )
      );
  ?>
  <md-button class="md-raised md-primary"
             ng-cloak
             ng-if="vm.selectedLanguage"
             ng-href="<?= $baseUrl ?>/{{vm.selectedLanguage.code}}/none">
    <?= format(
            /* @translators: button appearing after a language search
                             in Browse by languages page */
            __('Show all sentences in {language}'),
            ['language' => '{{vm.selectedLanguage.name}}']
        ) ?>
  </md-button>
</div>
