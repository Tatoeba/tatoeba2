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
        .factory('searchService', ['$httpParamSerializer', '$window', function($httpParamSerializer, $window) {
            return {
               submit: submit
            };

            function submit(form, filters, target = 'search') {
                if (!form.$valid) {
                    return;
                }

                var params = angular.copy(filters);
                ['from', 'to', 'trans_to'].forEach(function(filter) {
                    if (filter in params) {
                        params[filter] = params[filter] ? params[filter].code : '';
                    }
                });
                var url = get_tatoeba_root_url()
                          + '/sentences/' + target + '?'
                          + $httpParamSerializer(params);

                $window.location.href = url;
            }
        }]);
})();
