<?php
use Cake\Core\Configure;

if (!isset($displayOption)) {
    $displayOption = '';
}
if (!isset($class)) {
    $class = '';
}
if (!isset($label)) {
    $label = $this->Languages->getInterfaceLanguage();
}
?>
<md-button <?= $displayOption ?> class="<?= $class ?>" ng-click="showInterfaceLanguageSelection()">
    <md-icon>language</md-icon> <?= $label ?>
</md-button>