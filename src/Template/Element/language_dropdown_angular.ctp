<div class="language-dropdown-container"
     title="{{selectedLanguage.name}}">
    <input type="hidden" name="{{name}}" value="{{selectedLanguage.code}}" autocomplete="off">
    <md-autocomplete
        ng-cloak
        md-input-id="{{inputId}}"
        md-menu-class="language-dropdown"
        md-selected-item="selectedLanguage"
        md-selected-item-change="vm.onSelectedLanguageChange()"
        md-search-text="vm.searchText"
        md-search-text-change="vm.onSearchTextChange()"
        md-items="language in vm.querySearch(vm.searchText)"
        md-item-text="language.name"
        md-min-length="minLength"
        md-autoselect="vm.searchText.length"
        ng-blur="vm.onBlur()"
        ng-focus="vm.onFocus()"
        placeholder="{{placeholder}}">
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
