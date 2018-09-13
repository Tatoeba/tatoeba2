<?
$this->Html->script(JS_PATH . 'directives/language-dropdown.dir.js', array('block' => 'scriptBottom'));
$languagesJSON = htmlspecialchars(json_encode($languages), ENT_QUOTES, 'UTF-8');
$selectedLanguage = isset($selectedLanguage) ? $selectedLanguage : '';
$this->Form->unlockField($name);
?>
<div language-dropdown 
     ng-init="vm.init(<?= $languagesJSON ?>, '<?= $selectedLanguage ?>', '<?= $name ?>')" 
     class="language-dropdown-container"
     title="{{vm.selectedItem.name}}">
     <?= $this->Form->hidden($name, array('value' => '{{vm.selectedItem.code}}')); ?>
    <md-autocomplete md-select-on-focus
        md-menu-class="language-dropdown"
        md-selected-item="vm.selectedItem"
        md-search-text="vm.searchText"
        md-items="language in vm.querySearch(vm.searchText)"
        md-item-text="language.name"
        md-min-length="0"
        placeholder="<?= __('Enter a language') ?>">
        <md-item-template>
            <span md-highlight-text="vm.searchText" 
                  md-highlight-flags="^i">{{language.name}}</span>
        </md-item-template>
        <md-not-found>
        <?= __('No language found.') ?>
        </md-not-found>
    </md-autocomplete>
</div>