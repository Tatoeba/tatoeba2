<div class="language-dropdown-container">
    <md-autocomplete
        ng-cloak
        md-select-on-focus
        md-input-name="{{name}}"
        md-input-id="{{inputId}}"
        md-menu-class="language-dropdown"
        md-selected-item="selectedLanguage"
        md-selected-item-change="vm.onSelectedLanguageChange()"
        md-search-text="vm.searchText"
        md-search-text-change="vm.onSearchTextChange()"
        md-items="language in vm.querySearch(vm.searchText)"
        md-item-text="language.name"
        md-min-length="1-+(vm.hasSuggestions||vm.showAll)"
        md-autoselect="vm.autoselect"
        ng-blur="vm.onBlur()"
        ng-focus="vm.onFocus($event)"
        md-require-match="true"
        md-no-cache="vm.searchText == ''"
        placeholder="{{placeholder}}">
        <md-item-template>
           <svg class="language-icon" ng-if="vm.hasLanguageIcon(language.code)">
               <use ng-attr-xlink:href="{{vm.getLanguageIconSpriteUrl(language.code)}}"
                    xlink:href="" />
           </svg>

           <span md-highlight-text="vm.searchText"
                 md-highlight-flags="ig"
                 ng-class="{'priority-language': language.isPriority}">{{language.name}}</span>
        </md-item-template>
        <md-not-found>
            <?= __('No language found.') ?>
            <md-button class="md-primary"
                       ng-show="!vm.showAll"
                       ng-click="vm.showAll = true; vm.searchText = ''">
                <md-icon ng-cloak>keyboard_arrow_left</md-icon>
                <?php /* @translators: button in language dropdown to show all
                         languages. Appears when entered text returns no matches. */ ?>
                <?= __('Show all') ?>
            </md-button>
        </md-not-found>
    </md-autocomplete>
    <input type="hidden" name="{{name}}" value="{{selectedLanguage.code}}" autocomplete="off">
</div>
