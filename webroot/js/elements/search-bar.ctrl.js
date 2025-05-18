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
        .controller('SearchBarController', ['$scope', 'searchService', function($scope, search) {
            var vm = this;

            vm.langFrom = '';
            vm.langTo = '';
            
            vm.clearSearch = clearSearch;
            vm.swapLanguages = swapLanguages;
            vm.submit = submit;

            ///////////////////////////////////////////////////////////////////////////

            function clearSearch() {
                vm.searchQuery = '';
                angular.element(document.querySelector('#SentenceQuery')).focus();
            }

            function swapLanguages() {
                var newLangFrom = vm.langTo;
                var newLangTo = vm.langFrom;
                vm.langFrom = newLangFrom;
                vm.langTo = newLangTo;
            }

            function submit(form) {
                var filters = {
                    'query': vm.searchQuery,
                    'from' : vm.langFrom,
                    'to'   : vm.langTo
                };
                search.submit(form, filters);
            }
        }]);
})();
