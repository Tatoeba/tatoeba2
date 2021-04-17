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
        .controller('UsersRegisterController', ['$http', UsersRegisterController])
        .directive('uniqueUsername', uniqueUsername)
        .directive('uniqueEmail', uniqueEmail)
        .directive('serverError', serverError)
        .directive('input',  ['$parse', assignDefaultValuesToModel])
        .directive('select', ['$parse', assignDefaultValuesToModel]);

    const rootUrl = get_tatoeba_root_url();

    function assignDefaultValuesToModel($parse) {
        return {
            restrict: 'E',
            link: function (scope, element, attrs) {
                if (attrs.ngModel) {
                    var value;
                    element = element[0];
                    if (element.options) {
                        value = element.options[element.selectedIndex].value;
                    } else if (attrs.value !== undefined) {
                        value = attrs.value;
                    }

                    if (value !== undefined) {
                        $parse(attrs.ngModel).assign(scope, value);
                    }
                }
            }
        };
    };

    function UsersRegisterController() {
        var vm = this;

        vm.togglePassword = togglePassword;
        vm.isPasswordVisible = false;
        vm.confirm = false;

        ///////////////////////////////////////////////////////////////////////////

        function togglePassword() {
            vm.isPasswordVisible = !vm.isPasswordVisible;
            var type = vm.isPasswordVisible ? 'text' : 'password';
            angular.element(document.querySelector('#registrationPassword')).attr('type', type);
        }
    }

    function uniqueUsername($http, $q) {
        return {
            require: 'ngModel',
            link: function($scope, $elem, $attr, ngModel) {
                ngModel.$asyncValidators.uniqueUsername = function(value) {
                    if (!value) {
                        return $q.resolve();
                    }
                    return $http.get(rootUrl + '/users/check_username/' + value).then(
                        function(response) {
                            if (response.data === 'valid') {
                                return $q.resolve();
                            } else {
                                return $q.reject();
                            }
                        }
                    );
                }
            }
        };
    }

    function uniqueEmail($http, $q) {
        return {
            require: 'ngModel',
            link: function($scope, $elem, $attr, ngModel) {
                ngModel.$asyncValidators.uniqueEmail = function(value) {
                    if (!value) {
                        return $q.resolve();
                    }
                    return $http.get(rootUrl + '/users/check_email/' + value).then(
                        function(response) {
                            if (response.data === 'valid') {
                                return $q.resolve();
                            } else {
                                return $q.reject();
                            }
                        }
                    );
                }
            }
        };
    }

    function serverError() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function($scope, $elem, $attr, ngModel) {
                ngModel.$validators.serverError = function() {
                    return ngModel.$dirty;
                };

                window.setTimeout(function() {
                    ngModel.$setTouched();
                    $scope.$apply();
                }, 0);
            }
        };
    }
})();
