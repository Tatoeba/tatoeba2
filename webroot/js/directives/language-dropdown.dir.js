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
            scope: true,
            controllerAs: 'vm',
            controller: ['$scope', function($scope) {
                var vm = this;
                var languages = [];
                var dropdownName = '';

                vm.previousSelectedItem = languages[0];
                vm.selectedItem = null;
                vm.searchText = '';

                vm.init = init;
                vm.querySearch = querySearch;
                vm.onSelectedItemChange = onSelectedItemChange;
                vm.onSearchTextChange = onSearchTextChange;
                vm.onBlur = onBlur;
                vm.onFocus = onFocus;

                /////////////////////////////////////////////////////////////////////////

                $scope.$on('setLang', function(event, data){
                    if (data.dropdownName && data.dropdownName === dropdownName) {
                        setLang(data.lang);
                    }
                });

                /////////////////////////////////////////////////////////////////////////

                function init(data, selectedLang, ourDropDownName) {
                    var isPriority;
                    dropdownName = ourDropDownName;
                    
                    Object.keys(data).forEach(function (key1) {
                        if (typeof data[key1] === 'object'){
                            var items = data[key1];
                            isPriority = !isPriority && key1 !== '0';
                            Object.keys(items).forEach(function (key2) {
                                languages.push({code: key2, name: items[key2], isPriority: isPriority});
                            });
                        } else {
                            languages.push({code: key1, name: data[key1]});
                        }
                    });

                    if (selectedLang) {
                        setLang(selectedLang);
                    }
                }

                function querySearch(value) {
                    if (value) {
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
                        return languages;
                    }
                }

                function onSelectedItemChange(item) {
                    if (vm.selectedItem) {
                        $scope.$parent.$broadcast('languageChange', {dropdownName: dropdownName, lang: vm.selectedItem.code});
                    }            
                }

                function setLang(lang) {
                    vm.selectedItem = languages.find(function (item) {
                        return item.code === lang;
                    });
                    $scope.$parent.$broadcast('languageChange', {dropdownName: dropdownName, lang: lang});
                }

                function onSearchTextChange() {
                    vm.searchText = vm.searchText.replace(/\t/, ' ');
                }

                function onBlur() {
                    if (!vm.selectedItem) {
                        vm.selectedItem = vm.previousSelectedItem;
                    }
                }

                function onFocus() {
                    if (vm.selectedItem) {
                        vm.previousSelectedItem = vm.selectedItem;
                        vm.searchText = '';
                    }
                }
            }]
        };
    }

})();