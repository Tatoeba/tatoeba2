<div class="language-dropdown-container">
    <input type="hidden" name="{{name}}" value="{{selectedLanguage.code}}" autocomplete="off">
    <md-autocomplete
        ng-cloak
        md-select-on-focus
        md-input-id="{{inputId}}"
        md-menu-class="language-dropdown"
        md-selected-item="selectedLanguage"
        md-selected-item-change="vm.onSelectedLanguageChange()"
        md-search-text="vm.searchText"
        md-search-text-change="vm.onSearchTextChange()"
        md-items="language in vm.querySearch(vm.searchText)"
        md-item-text="language.name"
        md-min-length="minLength"
        md-autoselect="vm.autoselect"
        ng-blur="vm.onBlur()"
        ng-focus="vm.onFocus($event)"
        md-require-match="true"
        md-no-cache="vm.showAll"
        placeholder="{{placeholder}}">
        <md-item-template>
            <?php /* Warning: <li> injection hack ahead, expect missing opening tags */ ?>
                    <span md-highlight-text="vm.searchText"
                          md-highlight-flags="ig"
                          ng-class="{'priority-language': language.isPriority}">{{language.name}}</span>
                </md-autocomplete-parent-scope>
            </li>
            <li class="show-all"
                md-autocomplete-parent-scope
                ng-if="!$mdAutocompleteCtrl.notFoundVisible()">
                <md-button ng-show="!vm.showAll && vm.suggestionsDisplaying()"
                           class="md-primary"
                           ng-mouseup="vm.showAll = true">
                    <?php /* @translators: button in language dropdown
                                           to show all languages */ ?>
                    <?= __('Show all') ?>
                    <md-icon ng-cloak>keyboard_arrow_down</md-icon>
                </md-button>
            </li>
        </md-item-template>
        <md-not-found>
        <?= __('No language found.') ?>
        </md-not-found>
    </md-autocomplete>
</div>
