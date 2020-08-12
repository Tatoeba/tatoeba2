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
        .controller('VocabularyOfController', ['$http', VocabularyOfController]);

    function VocabularyOfController($http) {
        var vm = this;

        vm.remove = remove;

        ///////////////////////////////////////////////////////////////////////////

        function remove(id) {
            $http.get(get_tatoeba_root_url() + '/vocabulary/remove/' + id).then(
                function(response) {
                    angular.element(document.querySelector('#vocabulary_' + id)).remove();
                }
            );
        }
    }
})();
