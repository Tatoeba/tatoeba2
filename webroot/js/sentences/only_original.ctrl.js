/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

(function() {
    'use strict';

    angular
        .module('app')
        .controller('OriginalSentencesController', [
            '$scope', '$window', OriginalSentencesController
        ]);

    function OriginalSentencesController($scope, $window) {
        $scope.toggle = function() {
            var oldQuery = $window.location.search;
            if ($scope.original) {
                    var newQuery = oldQuery.replace(/only_original=&?/, '');
            } else {
                if (oldQuery.indexOf('?') > -1) {
                    var newQuery = oldQuery.replace('?', '?only_original=&');
                } else {
                    var newQuery = oldQuery + '?only_original=';
                }
            }
            $window.location.search = newQuery;
        }
    }

})();
