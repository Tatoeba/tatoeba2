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
        .directive(
            'sentenceAndTranslations',
            sentenceAndTranslations
        )
        // https://stackoverflow.com/questions/28851893/angularjs-textaera-enter-key-submit-form-with-autocomplete
        .directive('ngEnter', function() {
            return function(scope, element, attrs) {
                element.bind('keydown', function(e) {
                    if(e.which === 13) {
                        scope.$apply(function(){
                            scope.$eval(attrs.ngEnter, {'e': e});
                        });
                        e.preventDefault();
                    }
                });
            };
        });

    function sentenceAndTranslations() {
        return {
            scope: true,
            controller: SentenceAndTranslationsController,
            controllerAs: 'vm'
        };
    }

    function SentenceAndTranslationsController($http, $cookies) {
        const MAX_TRANSLATIONS = 5;
        const rootUrl = get_tatoeba_root_url();

        var vm = this;
        var allDirectTranslations = [];
        var alIndirectTranslations = [];

        vm.inProgress = false;
        vm.isExpanded = false;
        vm.isMenuExpanded = false;
        vm.isTranslationFormVisible = false;
        vm.expandableIcon = 'expand_more';
        vm.userLanguages = [];
        vm.directTranslations = [];
        vm.indirectTranslations = [];
        vm.newTranslation = {};

        vm.initUserLanguages = initUserLanguages;
        vm.initTranslations = initTranslations;
        vm.expandOrCollapse = expandOrCollapse;
        vm.toggleMenu = toggleMenu;
        vm.playAudio = playAudio;
        vm.getAudioAuthor = getAudioAuthor;
        vm.translate = translate;
        vm.saveTranslation = saveTranslation;
        vm.editTranslation = editTranslation;

        /////////////////////////////////////////////////////////////////////////

        function initUserLanguages(data) {
            vm.userLanguages = data;
        }

        function initTranslations(directTranslations, indirectTranslations) {
            allDirectTranslations = directTranslations;
            alIndirectTranslations = indirectTranslations;
            showFewerTranslations();
        }

        function expandOrCollapse() {
            vm.isExpanded = !vm.isExpanded;

            if (vm.isExpanded) {
                vm.expandableIcon = 'expand_less';
                showAllTranslations();
            } else {
                vm.expandableIcon = 'expand_more';
                showFewerTranslations();
            }
        }

        function showAllTranslations() {
            vm.directTranslations = allDirectTranslations;
            vm.indirectTranslations = alIndirectTranslations;
        }

        function showFewerTranslations() {
            vm.directTranslations = allDirectTranslations.filter(function(item, index) {
                return index <= MAX_TRANSLATIONS - 1;
            });
            vm.indirectTranslations = alIndirectTranslations.filter(function(item, index) {
                return index + allDirectTranslations.length <= MAX_TRANSLATIONS - 1;
            });
        }

        function toggleMenu() {
            vm.isMenuExpanded = !vm.isMenuExpanded;
        }

        function playAudio($event) {
            $event.stopPropagation();
            $event.preventDefault();

            var audioURL = $event.currentTarget.href;
            var audio = new Audio(audioURL);
            audio.play();
        }

        function getAudioAuthor(sentence) {
            var audio = sentence.audios ? sentence.audios[0] : null;

            if (audio && audio.user) {
                return audio.user.username;
            } else if (audio && audio.external) {
                return audio.external.username;
            } else {
                return null;
            }
        }

        function translate(id) {
            vm.isTranslationFormVisible = true;
            
            if (vm.newTranslation.editable) {
                vm.newTranslation = {};
            }

            if ($cookies.get('translationLang') && !vm.newTranslation.lang) {
                vm.newTranslation.lang = $cookies.get('translationLang');
            } else {
                vm.newTranslation.lang = 'auto';
            }

            focusTranslationInput(id);
        }

        function saveTranslation(sentenceId) {
            $cookies.put('translationLang', vm.newTranslation.lang);

            if (vm.newTranslation && vm.newTranslation.text) {
                vm.inProgress = true;
                if (vm.newTranslation.editable) {
                    var sentence = {
                        id: [vm.newTranslation.lang, vm.newTranslation.id].join('_'),
                        value: vm.newTranslation.text
                    };
                    $http.post(rootUrl + '/sentences/edit_sentence/json', sentence).then(function(result) {
                        vm.isTranslationFormVisible = false;
                        vm.inProgress = false;
                        vm.newTranslation = {};
                    });
                } else {
                    var data = {
                        id: sentenceId,
                        selectLang: vm.newTranslation.lang,
                        value: vm.newTranslation.text
                    };
                    $http.post(rootUrl + '/sentences/save_translation/json', data).then(function(result) {
                        result.data.editable = true;
                        result.data.parentId = sentenceId;
                        allDirectTranslations.unshift(result.data);
                        vm.newTranslation = {};
                        vm.isTranslationFormVisible = false;
                        vm.inProgress = false;
                        showFewerTranslations();
                    });
                }
            }
        }

        function editTranslation(translation) {
            vm.isTranslationFormVisible = true;
            vm.newTranslation = translation;
            focusTranslationInput(translation.parentId);
        }

        function focusTranslationInput(id) {
            setTimeout(function() {
                var input = angular.element('#translation-form-' + id);
                input.focus();
            }, 100);
        }
    }

})();