<?php
use Cake\Core\Configure;

if (!isset($displayOption)) {
    $displayOption = '';
}
$label = $this->Languages->getInterfaceLanguage();
?>
<md-button <?= $displayOption ?> ng-click="showInterfaceLanguageSelection()">
    <md-icon>language</md-icon> <?= $label ?>
</md-button>