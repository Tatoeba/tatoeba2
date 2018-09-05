<?
$this->Html->script(JS_PATH . 'directives/language-dropdown.dir.js', array('block' => 'scriptBottom'));
$languagesJSON = htmlspecialchars(json_encode($languages), ENT_QUOTES, 'UTF-8');
?>
<div language-dropdown 
     ng-init="vm.init(<?= $languagesJSON ?>, '<?= $selectedLanguage ?>', '<?= $name ?>')" 
     class="language-dropdown-container"
     title="{{vm.selectedItem.name}}">
    <input name="<?= $name ?>" type="hidden" value="{{vm.selectedItem.code}}">
    <md-autocomplete  
        md-menu-class="language-dropdown"
        md-selected-item="vm.selectedItem"
        md-search-text="vm.searchText"
        md-items="language in vm.querySearch(vm.searchText)"
        md-item-text="language.name"
        md-min-length="0">
        <md-item-template>
            <span md-highlight-text="vm.searchText" 
                  md-highlight-flags="^i">{{language.name}}</span>
        </md-item-template>
        <md-not-found>
        <?= __('No language found.') ?>
        </md-not-found>
    </md-autocomplete>
</div>