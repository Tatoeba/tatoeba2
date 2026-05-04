<?php
$languagesJSON = h(json_encode($languages));
$initialSelection = isset($initialSelection) ? $initialSelection : '';
$placeholder = isset($placeholder) ? $placeholder : __('Select a language');

$this->AngularTemplate->addTemplate(
        $this->element('language_dropdown_angular'),
        'language-dropdown-template'
);

try {
    $this->Form->unlockField($name);
} catch (\Cake\Core\Exception\Exception $e) {
    // simply do nothing if no form was created
}
?>
<language-dropdown
<?php if (isset($id)): ?>
    input-id="<?= $id ?>"
<?php endif; ?>
    name="<?= $name ?>"
    languages-json="<?= $languagesJSON ?>"
<?php if (isset($selectedLanguage)): ?>
    selected-language="<?= $selectedLanguage ?>"
<?php endif; ?>
<?php if (isset($onSelectedLanguageChange)): ?>
    on-selected-language-change="<?= $onSelectedLanguageChange ?>"
<?php endif; ?>
    initial-selection="<?= $initialSelection ?>"
    placeholder="<?= $placeholder ?>"
    force-item-selection="<?= $forceItemSelection ?? false ?>"
<?php if ($alwaysShowAll ?? false): ?>
    always-show-all="true"
<?php endif; ?>
></language-dropdown>
