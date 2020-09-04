<?php
$languagesJSON = h(json_encode($languages));
$initialSelection = isset($initialSelection) ? $initialSelection : '';
$placeholder = isset($placeholder) ? $placeholder : __('Select a language');


// Add template to the AngularJS cache, but only if it's not there yet
$existingScripts = $this->fetch('scriptBottom');
if (strpos($existingScripts, 'id="language-dropdown-template"') === false) {
    $this->Html->scriptBlock(
        $this->element('language_dropdown_angular'),
        [
            'block' => 'scriptBottom',
            'type' => 'text/ng-template',
            'id' => 'language-dropdown-template'
        ]
    );
}


$this->Form->unlockField($name);
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
></language-dropdown>
