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
        .directive('uniqueEmail', uniqueEmail);

    function UsersRegisterController() {
        var vm = this;

        vm.togglePassword = togglePassword;
        vm.isPasswordVisible = false;

        ///////////////////////////////////////////////////////////////////////////

        function togglePassword() {
            vm.isPasswordVisible = !vm.isPasswordVisible;
            var type = vm.isPasswordVisible ? 'text' : 'password';
            $('#registrationPassword').attr('type', type);
        }
    }

    function uniqueUsername($http, $q) {
        return {
            require: 'ngModel',
            link: function($scope, $elem, $attr, ngModel) {
                ngModel.$asyncValidators.uniqueUsername = function(value) {
                    return $http.get('/users/check_username/' + value).then(
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
                    return $http.get('/users/check_email/' + value).then(
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
})();
