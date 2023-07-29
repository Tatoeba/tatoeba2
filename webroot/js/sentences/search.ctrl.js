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
        .directive('ngModelInit',  ['$parse', function($parse) {
            return {
                restrict: 'A',
                require: '?ngModel',
                link: function (scope, element, attrs) {
                    if (attrs.ngModel) {
                        $parse(attrs.ngModel).assign(scope, attrs.ngModelInit);
                    }
                }
            };
        }])
        // This directive comes from https://code.angularjs.org/1.8.0/docs/error/ngModel/numfmt
        .directive('stringToNumber', function() {
            return {
                require: 'ngModel',
                link: function(scope, element, attrs, ngModel) {
                    ngModel.$parsers.push(function(value) {
                        return '' + (value ?? '');
                    });
                    ngModel.$formatters.push(function(value) {
                        return parseFloat(value);
                    });
                }
            };
        })
        .controller('SearchController', ['searchService', function(search) {
            var vm = this;

            vm.submit = submit;

            ///////////////////////////////////////////////////////////////////////////

            function submit(form, filters, target) {
                search.submit(form, filters, target);
            }
        }]);
})();
