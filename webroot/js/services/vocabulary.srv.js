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
        .factory('vocabularyService', ['$mdDialog', '$http', function($mdDialog, $http) {
            return {
               edit: edit
            };

            function edit(vocab, canEdit) {
                $mdDialog.show({
                    controller: DialogController,
                    controllerAs: 'ctrl',
                    clickOutsideToClose: true,
                    templateUrl: get_tatoeba_root_url() + '/angular_templates/edit_vocabulary'
                });

                function DialogController() {
                    var ctrl = this;

                    ctrl.vocab = vocab;
                    ctrl.canEdit = canEdit;

                    ctrl.save = function (vocab) {
                        vocab.lang = ctrl.selected_lang.code;
                        $http.post(get_tatoeba_root_url() + '/vocabulary/edit/' + vocab.id, vocab).then(
                            function success(response) {
                                location.reload();
                            },
                            function error(response) {
                                location.reload();
                            }
                        );
                    }

                    ctrl.remove = function (vocab) {
                        $http.get(get_tatoeba_root_url() + '/vocabulary/remove/' + vocab.id).then(
                            function success(response) {
                                angular.element(document.querySelector('#vocabulary_' + vocab.id)).remove();
                                $mdDialog.cancel();
                            },
                            function error(response) {
                                $mdDialog.cancel();
                            }
                        );
                    }

                    ctrl.close = function() {
                        $mdDialog.cancel();
                    };
                }
            }
        }]);
})();
