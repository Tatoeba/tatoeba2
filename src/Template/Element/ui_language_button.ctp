<?php
use Cake\Core\Configure;

if (!isset($displayOption)) {
    $displayOption = '';
}
$langCode = Configure::read('Config.language');
$lang = array_filter(
    Configure::read('UI.languages'),
    function ($item) use ($langCode) {
        return $item[0] == $langCode;
    }
);
$label = current($lang)[2];
?>
<md-button <?= $displayOption ?> class="ui-lang-select-mobile" ng-click="showInterfaceLanguageSelection()">
    <md-icon>language</md-icon> <?= $label ?>
</md-button>