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
        .controller('VocabularyAddSentencesController', [
            '$http', 'vocabularyService', VocabularyAddSentencesController
        ]);

    function VocabularyAddSentencesController($http, vocabularyService) {
        var vm = this;

        vm.showForm = showForm;
        vm.hideForm = hideForm;
        vm.saveSentence = saveSentence;
        vm.edit = vocabularyService.edit;

        vm.sentencesAdded = {};

        ///////////////////////////////////////////////////////////////////////////

        function showForm(id) {
            angular.element(document.querySelector('#form_' + id)).removeClass('ng-hide');
            angular.element(document.querySelector('#form_' + id + '_input')).focus();
        }

        function hideForm(id) {
            angular.element(document.querySelector('#form_' + id)).addClass('ng-hide');
        }

        function saveSentence(id, lang) {
            var loader = angular.element(document.querySelector('#loader_' + id));
            var body = {
                'text': vm.sentence[id],
                'lang': lang
            };

            if (!vm.sentencesAdded[id]) {
                vm.sentencesAdded[id] = [];
            }

            loader.removeClass('ng-hide');
            hideForm(id);

            var rootUrl = get_tatoeba_root_url();
            var req = {
                method: 'POST',
                url: rootUrl + '/vocabulary/save_sentence/' + id,
                headers: {
                    'X-CSRF-Token': get_csrf_token()
                },
                data: body
            }

            $http(req).then(
                function(response) {
                    loader.addClass('ng-hide');

                    vm.sentencesAdded[id].push(response.data);
                    vm.sentence[id] = null;
                }
            );
        }

    }
})();
