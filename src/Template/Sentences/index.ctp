<?php

use App\Model\CurrentUser;

$this->Html->script('/js/sentences/index.ctrl.js', ['block' => 'scriptBottom']);
$title = __('Language index');
$this->set('title_for_layout', $this->Pages->formatTitle($title));
$header = format(
  __n(
       'There is {n} language on Tatoeba',
       'There are {n} languages on Tatoeba',
       $nbrLanguages
  ),
  ['n' => $nbrLanguages]
);
$top10 = $this->ShowAll->extractTopTen($stats);
$profileLangs = $this->ShowAll->extractLanguageProfiles($stats);
?>
<div id="content-container">
<section ng-controller="SentencesIndexController as vm">

<h1><?= $header ?></h1>

<?php if (CurrentUser::isMember()): ?>
  <md-tabs md-dynamic-height md-center-tabs>
    <md-tab label="<?= h(__('My profile languages')) ?>">
      <md-content class="profile-languages">
        <?php foreach ($profileLangs as $lang): ?>
          <div class="profile-language lang-card">
            <?= $this->element('sentences/index/lang_card', $lang->toArray()); ?>
          </div>
        <?php endforeach; ?>
        <?= $this->element('sentences/index/search') ?>
      </md-content>
    </md-tab>

    <md-tab label="<?= h(__('Top ten')) ?>">
      <md-content>
        <?= $this->element('sentences/index/top10', compact('top10')) ?>
      </md-content>
    </md-tab>

    <md-tab label="<?= h(__('All languages')) ?>">
      <md-content>
        <?= $this->element('sentences/index/all', compact('stats')) ?>
      </md-content>
    </md-tab>
  </md-tabs>

<?php else: /* guests */ ?>

  <?= $this->element('sentences/index/top10', compact('top10')) ?>

  <?= $this->element('sentences/index/search') ?>

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
    <?= $this->element('sentences/index/all', compact('stats')) ?>
  </div>

<?php endif; ?>

</section>
</div>
