<div id="logo-container">
  <img ng-cloak class="centered-logo" src="/img/tatoeba.svg" width="200" />
</div>

<div ng-cloak class="languages-around-logo">
  <?php foreach ($top10 as $index => $lang): ?>
    <div class="language-around-logo lang-card lang<?= ($index+1) ?>">
      <?= $this->element('sentences/index/lang_card', $lang->toArray()); ?>
    </div>
  <?php endforeach; ?>
</div>
