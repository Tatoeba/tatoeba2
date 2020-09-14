<?php
$languagesJSON = h(json_encode($languages));
$selectedLanguage = isset($selectedLanguage) ? $selectedLanguage : '';
$placeholder = isset($placeholder) ? $placeholder : __('Select a language');
$this->Form->unlockField($name);
$openOnFocus = isset($openOnFocus) ? $openOnFocus : true;
?>
<div language-dropdown 
     ng-init="vm.init(<?= $languagesJSON ?>, '<?= $selectedLanguage ?>', '<?= $name ?>')" 
     class="language-dropdown-container"
     title="{{vm.selectedItem.name}}">
     <?= $this->Form->hidden($name, array('value' => '{{vm.selectedItem.code}}')); ?>
    <md-autocomplete
        ng-cloak
<?php if (isset($id)): ?>
        md-input-id="<?= $id ?>"
<?php endif; ?>
        md-menu-class="language-dropdown"
        md-selected-item="vm.selectedItem"
        md-selected-item-change="vm.onSelectedItemChange()"
        md-search-text="vm.searchText"
        md-search-text-change="vm.onSearchTextChange()"
        md-items="language in vm.querySearch(vm.searchText)"
        md-item-text="language.name"
        md-min-length="<?= (int)!$openOnFocus; ?>"
        md-autoselect="vm.searchText.length"
        ng-blur="vm.onBlur()"
        ng-focus="vm.onFocus()"
        placeholder="<?= $placeholder ?>">
        <md-item-template>
            <span md-highlight-text="vm.searchText" 
                  md-highlight-flags="ig"
                  ng-class="{'priority-language': language.isPriority}">{{language.name}}</span>
        </md-item-template>
        <md-not-found>
        <?= __('No language found.') ?>
        </md-not-found>
    </md-autocomplete>
</div>