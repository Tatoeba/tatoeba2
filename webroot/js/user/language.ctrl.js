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
        .controller('LanguageController', ['$scope', '$http', LanguageController])
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
                    if ($scope.level !== null) {
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

    function LanguageController($scope, $http) {
        var vm = this;

        vm.init = init;
        vm.addLangNextStep = addLangNextStep;
        vm.resetForm = resetForm;
        vm.langs = [];
        resetForm();

        function addLangNextStep() {
            switch (vm.addLangStep) {
            case '':
                vm.addLangStep = 'selection';
                break;
            case 'selection':
                vm.selectedLang.level = null;
                vm.addLangStep = 'level';
                break;
            case 'level':
                vm.addLangStep = 'details';
                break;
            case 'details':
                var req = {
	            language_code: vm.selectedLang.code,
	            level:         vm.selectedLang.level,
	            details:       vm.selectedLang.details
                };
                var url = get_tatoeba_root_url() + '/users_languages/save';
                vm.addLangStep = 'loading';
                $http.post(url, req)
                     .then(
                         function(response) {
                             vm.langs = response.data.languages;
                             vm.addLangStep = '';
                         },
                         function(response) {
                             vm.error = response.data.message;
                             vm.addLangStep = 'error';
                         }
                     );
                break;
            case 'error':
                vm.resetForm();
                break;
            }
        }

        function init(userLangs) {
            vm.langs = userLangs;
        }

        function resetForm() {
            vm.addLangStep = ''; // can be '', 'selection', 'level', 'details', 'loading', 'error'
            vm.selectedLang = null;
            vm.error = '';
        }
    }
})();
