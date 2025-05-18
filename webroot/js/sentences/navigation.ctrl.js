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
        .controller('SentencesNavigationController', [
            '$http', '$scope', SentencesNavigationController
        ]);

    function SentencesNavigationController($http, $scope) {
        var vm = this;

        vm.lang = null;
        vm.currentId = null;
        vm.next = null;
        vm.prev = null;

        vm.init = init;
        vm.updateNavigation = updateNavigation;
        vm.onSelectedLanguageChange = onSelectedLanguageChange;

        ///////////////////////////////////////////////////////////////////////////

        function onSelectedLanguageChange(language) {
            vm.lang = language.code;
            if (vm.currentId) {
                updateNavigation(vm.currentId, vm.lang);
            }
        }

        ///////////////////////////////////////////////////////////////////////////

        function init(lang, currentId, prev, next) {
            vm.lang = lang ? lang : 'und';
            vm.currentId = currentId;
            vm.prev = prev;
            vm.next = next;
        }

        function updateNavigation(id, lang) {
            var rootUrl = get_tatoeba_root_url();
            var url = rootUrl + '/sentences/get_neighbors_for_ajax/' + id + '/' + lang;
            $http.get(url).then(
                function(response) {
                    if (response.data) {
                        vm.next = response.data.next;
                        vm.prev = response.data.prev;
                    }
                }
            );
        }
    }

})();
