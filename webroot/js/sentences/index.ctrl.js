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
        .controller('SentencesIndexController', ['$scope', function($scope) {
            var vm = this;

            vm.selectedLanguage = null;

            $scope.$on('languageChange', function(event, data) {
                if (data.dropdownName === 'ShowAllIn') {
                    vm.selectedLanguage = {'name': data.langName, 'code': data.lang};
                }
            });

            $scope.getFlagSpriteUrl = function (lang) {
                return '/cache_svg/allflags.svg#' + lang;
            };
        }]);
})();
