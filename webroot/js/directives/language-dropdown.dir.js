/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

(function() {
    'use strict';

    angular
        .module('app')
        .directive(
            'languageDropdown',
            languageDropdown
        );

    function languageDropdown() {
        return {
            scope: {
                inputId: '@?',
                name: '@',
                languagesJson: '<',
                selectedLanguage: '=?',
                onSelectedLanguageChange: '&?',
                initialSelection: '@?',
                placeholder: '@',
                forceItemSelection: '<',
                alwaysShowAll: '<',
            },
            templateUrl: 'language-dropdown-template',
            controllerAs: 'vm',
            controller: ['$scope', '$window', function($scope, $window) {
                var vm = this;
                var languages = [];
                var havingFocus = false;
                const SUGGESTIONS_MARKER_HACK = '\x0d';

                vm.previousSelectedItem = null;
                vm.searchText = '';
                vm.hasSuggestions = !!$scope.alwaysShowAll;
                vm.autoselect = true;
                vm.showAll = !!$scope.alwaysShowAll;

                vm.$onInit = $onInit;
                vm.querySearch = querySearch;
                vm.onSelectedLanguageChange = onSelectedLanguageChange;
                vm.onSearchTextChange = onSearchTextChange;
                vm.onBlur = onBlur;
                vm.onFocus = onFocus;
                vm.getLanguageIconSpriteUrl = getLanguageIconSpriteUrl;
                vm.hasLanguageIcon = hasLanguageIcon;

                /////////////////////////////////////////////////////////////////////////

                function $onInit() {
                    var data = $scope.languagesJson;
                    var isPriority;

                    Object.keys(data).forEach(function (key1) {
                        if (typeof data[key1] === 'object'){
                            var items = data[key1];
                            isPriority = !isPriority && key1 !== '0';
                            Object.keys(items).forEach(function (key2) {
                                languages.push({code: key2, name: items[key2], isPriority: isPriority});
                            });
                            vm.hasSuggestions = true;
                        } else {
                            var code = key1;
                            var lang = {code: code, name: data[code]};
                            if (code == 'und' || code == 'none') {
                                lang.isPriority = true;
                            }
                            languages.push(lang);
                        }
                    });

                    if ($scope.initialSelection) {
                        setLang($scope.initialSelection);
                    }
                }

                function querySearch(value) {
                    vm.autoselect = true;

                    if (!value.endsWith(SUGGESTIONS_MARKER_HACK) && value) {
                        var search = value.toLowerCase();
                        return languages.filter(function (item) {
                            var language = item.name.toLowerCase();
                            return language.indexOf(search) > -1;
                        }).sort(function(itemA, itemB) {
                            var nameA = itemA.name.toLowerCase();
                            var nameB = itemB.name.toLowerCase();
                            return nameA.indexOf(search) > nameB.indexOf(search);
                        });
                    } else {
                        if (vm.showAll)  {
                            return languages;
                        } else {
                            var results = languages.filter(function (item) {
                                return item.isPriority;
                            });
                            return results.length ? results : languages;
                        }
                    }
                }

                function setLang(lang) {
                    var newLang = languages.find(function (item) {
                        return item.code === lang;
                    });
                    if (newLang) {
                        $scope.selectedLanguage = newLang;
                    }
                }

                function onSelectedLanguageChange() {
                    if ((!$scope.forceItemSelection
                         || $scope.selectedLanguage
                         && $scope.selectedLanguage != vm.previousSelectedItem)
                        && $scope.onSelectedLanguageChange
                    ) {
                        $scope.onSelectedLanguageChange({
                            'window': $window,
                            'language': $scope.selectedLanguage,
                        });
                    }
                }

                function onSearchTextChange() {
                    if (typeof vm.searchText === 'string') {
                        vm.searchText = vm.searchText.replace(/\t/, ' ');
                    }
                }

                function onBlur() {
                    if ($scope.forceItemSelection) {
                        if (!$scope.selectedLanguage) {
                            $scope.selectedLanguage = vm.previousSelectedItem;
                        }
                    }
                    if (vm.searchText.endsWith(SUGGESTIONS_MARKER_HACK)) {
                        vm.searchText = vm.searchText.replace(SUGGESTIONS_MARKER_HACK, '');
                        $scope.selectedLanguage = vm.previousSelectedItem;
                    }
                    havingFocus = false;
                    vm.showAll = !!$scope.alwaysShowAll;
                }

                function onFocus($event) {
                    if (havingFocus || $event.target.tagName != 'INPUT') {
                        // we are sometimes getting called for no reason...
                        return;
                    }
                    if ($scope.selectedLanguage) {
                        vm.previousSelectedItem = $scope.selectedLanguage;
                    }
                    const clearButtonPressed =
                        $event.relatedTarget &&
                        $event.relatedTarget.tagName == 'BUTTON' &&
                        $event.relatedTarget.parentNode == $event.target.parentNode;
                    if (vm.hasSuggestions && vm.searchText !== '' && !clearButtonPressed) {
                        vm.autoselect = false;
                        vm.searchText += SUGGESTIONS_MARKER_HACK;
                    }
                    havingFocus = true;
                }

                function hasLanguageIcon(code) {
                    // naive yet working implementation
                    return code != 'und' && code != 'none';
                }

                function getLanguageIconSpriteUrl(code) {
                    return '/cache_svg/allflags.svg#' + code;
                }
            }]
        };
    }

})();
