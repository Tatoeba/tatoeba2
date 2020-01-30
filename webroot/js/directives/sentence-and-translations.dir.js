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
        })
        .directive('ngEscape', function() {
            return function(scope, element, attrs) {
                element.bind('keydown', function(e) {
                    if(e.which === 27) {
                        scope.$apply(function(){
                            scope.$eval(attrs.ngEscape, {'e': e});
                        });
                        e.preventDefault();
                    }
                });
            };
        })
        .directive('languageIcon', function() {
            return {
                restrict: 'E',
                scope: {
                    lang: '=',
                    title: '='
                },
                link: function($scope) {
                    if (!$scope.lang) {
                        $scope.lang = 'unknown';
                    }
                },
                template: `
                    <img class="language-icon" width="30" heigth="20" 
                      ng-attr-title="{{title ? title : lang}}"
                      ng-src="/img/flags/{{lang}}.svg" />
                `
            }
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
        var oldSentence = null;
        var allDirectTranslations = [];
        var allIndirectTranslations = [];

        vm.inProgress = false;
        vm.isExpanded = false;
        vm.isMenuExpanded = false;
        vm.isTranslationFormVisible = false;
        vm.isSentenceFormVisible = false;
        vm.expandableIcon = 'expand_more';
        vm.userLanguages = [];
        vm.sentence = null;
        vm.directTranslations = [];
        vm.indirectTranslations = [];
        vm.newTranslation = {};

        vm.init = init;
        vm.expandOrCollapse = expandOrCollapse;
        vm.toggleMenu = toggleMenu;
        vm.playAudio = playAudio;
        vm.getAudioAuthor = getAudioAuthor;
        vm.translate = translate;
        vm.saveTranslation = saveTranslation;
        vm.editTranslation = editTranslation;
        vm.edit = edit;
        vm.cancelEdit = cancelEdit;
        vm.editSentence = editSentence;

        /////////////////////////////////////////////////////////////////////////

        function init(langs, sentence, directTranslations, indirectTranslations) {
            vm.userLanguages = langs;
            initSentence(sentence);
            allDirectTranslations = directTranslations;
            allIndirectTranslations = indirectTranslations;
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
            vm.indirectTranslations = allIndirectTranslations;
        }

        function showFewerTranslations() {
            vm.directTranslations = allDirectTranslations.filter(function(item, index) {
                return index <= MAX_TRANSLATIONS - 1;
            });
            vm.indirectTranslations = allIndirectTranslations.filter(function(item, index) {
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
            vm.isSentenceFormVisible = false;
            
            if (vm.newTranslation.editable) {
                vm.newTranslation = {};
            }

            if ($cookies.get('translationLang') && !vm.newTranslation.text) {
                vm.newTranslation.lang = $cookies.get('translationLang');
            } else if (!vm.newTranslation.lang) {
                vm.newTranslation.lang = vm.userLanguages.length > 1 ? 'auto' : Object.keys(vm.userLanguages)[0];
            }
            focusInput('#translation-form-' + id);
        }

        function saveTranslation(sentenceId) {
            $cookies.put('translationLang', vm.newTranslation.lang);

            if (vm.newTranslation && vm.newTranslation.text) {
                vm.inProgress = true;
                if (vm.newTranslation.editable) {
                    saveSentence(vm.newTranslation).then(function(result) {
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
            focusInput('#translation-form-' + translation.parentId);
        }

        function focusInput(id) {
            setTimeout(function() {
                var input = angular.element(id);
                input.focus();
            }, 100);
        }

        function edit() {
            vm.isSentenceFormVisible = true;
            vm.isTranslationFormVisible = false;
            focusInput('#sentence-form-' + vm.sentence.id);
        }
        
        function cancelEdit() {
            vm.isSentenceFormVisible = false;
            initSentence(oldSentence);
        }

        function editSentence() {
            vm.inProgress = true;

            saveSentence(vm.sentence).then(function(result) {
                vm.isSentenceFormVisible = false;
                vm.inProgress = false;
                initSentence(result.data);
            });
        }

        function saveSentence(sentence) {
            var lang = sentence.lang === 'unknown' ? '' : sentence.lang;
            var data = {
                id: [lang, sentence.id].join('_'),
                value: sentence.text
            };
            return $http.post(rootUrl + '/sentences/edit_sentence/json', data);
        }

        function initSentence(data) {
            data.lang = data.lang ? data.lang : 'unknown';
            oldSentence = data;
            vm.sentence = Object.assign({}, data);
        }
    }

})();