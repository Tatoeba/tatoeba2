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
        .controller('LanguageController', ['$scope', LanguageController])
        .directive('languageLevel', function() {
            return {
                restrict: 'E',
                scope: {
                    level: '=',
                },
                link: function($scope) {
                    if ($scope.level === "") {
                        $scope.level = null;
                    }
                    if ($scope.level != null) {
                        $scope.level = parseInt($scope.level);
                    }
                },
                template:
                    // magic number 5 is the maximum possible level
                    '<div ng-if="level != null" class="languageLevel">' +
                      '<md-icon ng-repeat="n in [].constructor(level) track by $index" class="md-primary">star</md-icon>' +
                      '<md-icon ng-repeat="n in [].constructor(5-level) track by $index">star_border</md-icon>' +
                    '</div>'
            }
        });

    function LanguageController($scope) {
        var vm = this;

        vm.init = init;
        vm.addLangNextStep = addLangNextStep;
        vm.addLangCancel = addLangCancel;
        vm.langs = [];
        vm.addLangStep = ''; // can be '', 'selection', 'level' or 'details'
        vm.selectedLang = null;

        function addLangNextStep() {
            switch (vm.addLangStep) {
            case '':
                vm.addLangStep = 'selection';
                break;
            case 'selection':
                vm.addLangStep = 'level';
                break;
            case 'level':
                vm.addLangStep = 'details';
                break;
            case 'details':
                vm.addLangStep = '';
                break;
            }
        }

        $scope.$on('languageChange', function(event, data) {
            vm.selectedLang = {
               code: data.lang,
               name: data.langName,
               level: null
            };
        });

        function init(userLangs) {
            vm.langs = userLangs;
        }

        function addLangCancel() {
            vm.addLangStep = '';
            vm.selectedLang = null;
        }
    }
})();
