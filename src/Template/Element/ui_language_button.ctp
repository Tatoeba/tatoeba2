<md-button <?= $displayOption ?? '' ?> class="<?= $class ?? '' ?>" ng-click="showInterfaceLanguageSelection()">
    <md-icon>language</md-icon> 
    <?= $label ?? $this->Languages->getInterfaceLanguage() ?>
</md-button>