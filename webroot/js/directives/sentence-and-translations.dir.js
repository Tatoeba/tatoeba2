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
                template: 
                    '<img class="language-icon" width="30" heigth="20" ' +
                      'ng-attr-title="{{title ? title : lang}}"' +
                      'ng-src="/img/flags/{{lang}}.svg" />'
            }
        });

    function sentenceAndTranslations() {
        return {
            scope: true,
            controller: SentenceAndTranslationsController,
            controllerAs: 'vm'
        };
    }
    
    SentenceAndTranslationsController.$inject = ['$rootScope', '$scope', '$http', '$cookies', '$timeout', '$injector'];
    function SentenceAndTranslationsController($rootScope, $scope, $http, $cookies, $timeout, $injector) {
        const MAX_TRANSLATIONS = 5;
        const rootUrl = get_tatoeba_root_url();

        var vm = this;
        var oldSentence = null;
        var allDirectTranslations = [];
        var allIndirectTranslations = [];
        var allLists = [];
        var lastSelectedList = null;
        var timeout;
        var listsDataService;

        vm.menu = {};
        vm.inProgress = false;
        vm.isExpanded = false;
        vm.isMenuExpanded = false;
        vm.expandableIcon = 'expand_more';
        vm.userLanguages = [];
        vm.showAutoDetect = false;
        vm.sentence = null;
        vm.directTranslations = [];
        vm.indirectTranslations = [];
        vm.newTranslation = {};
        vm.lists = [];
        vm.listSearch = '';
        vm.visibility = {
            'translations': true,
            'translate_form': false,
            'sentence_form': false,
            'list_form': false
        };
        vm.selectedLangForRandom = null;

        vm.init = init;
        vm.initMenu = initMenu;
        vm.initLists = initLists;
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
        vm.favorite = favorite;
        vm.adopt = adopt;
        vm.list = list;
        vm.closeList = closeList;
        vm.toggleList = toggleList;
        vm.addToNewList = addToNewList;
        vm.searchList = searchList;
        vm.listType = 'of_user';
        vm.show = show;
        vm.hide = hide;

        /////////////////////////////////////////////////////////////////////////

        $scope.$on('newListCreated', function(event, data, sentenceId) {
            if (data === 'error') {
                resetLists();
                return;
            }
            var list = angular.copy(data);
            $cookies.put('most_recent_list', list.id);
            list.hasSentence = vm.sentence.id === parseInt(sentenceId);
            list.is_mine = '1';
            allLists.unshift(list);
            resetLists();
        });

        $scope.$on('randomSentenceRequested', function(event, lang) {
            var url = rootUrl + '/sentences/random';
            if (lang) {
                url += '/' + lang;
            }
            vm.inProgress = true;
            $http.get(url).then(function(result) {
                var sentence = result.data.random;
                var directTranslations = sentence.translations[0];
                var indirectTranslations = sentence.translations[1];
                init(vm.userLanguages, sentence, directTranslations, indirectTranslations);
                initMenu(false, sentence.menu);
                vm.inProgress = false;
            });
        });

        $scope.$on('menuToggled', function(event, isMenuExpanded) {
            vm.isMenuExpanded = isMenuExpanded;
        });

        /////////////////////////////////////////////////////////////////////////

        function init(langs, sentence, directTranslations, indirectTranslations) {
            vm.userLanguages = langs;
            vm.showAutoDetect = Object.keys(langs).length > 1;
            initSentence(sentence);
            allDirectTranslations = directTranslations ? directTranslations : [];
            allIndirectTranslations = indirectTranslations ? indirectTranslations : [];
            showFewerTranslations();
            initLists(sentence.sentences_lists);
        }

        function initMenu(isExpanded, menu) {
            if (isExpanded) {
                vm.isMenuExpanded = isExpanded;
            } else {
                vm.isMenuExpanded = $cookies.get('sentence_menu_expanded') === 'true';
            }
            vm.menu = menu;
        }

        function initLists(selectedLists) {
            if (!listsDataService) {
                listsDataService = $injector.get('listsDataService');
            }
            var selectableLists = listsDataService.getLists();

            if (selectableLists) {
                if (selectedLists) {
                    allLists = selectableLists.map(function(selectableList) {
                        var item = selectedLists.find(function(selectedList) {
                            return selectedList.id === selectableList.id;
                        });
                        selectableList.hasSentence = item !== undefined;
                        return selectableList;
                    });   
                } else {
                    allLists = selectableLists;
                }
                resetLists();
            } else {
                allLists = [];
            }
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
            $cookies.put('sentence_menu_expanded', vm.isMenuExpanded);
            $rootScope.$broadcast('menuToggled', vm.isMenuExpanded);
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
            show('translation_form');
            
            if (vm.newTranslation.editable) {
                vm.newTranslation = {};
            }

            var preselectedLang = $cookies.get('translationLang');
            var langCodes = Object.keys(vm.userLanguages);
            if (langCodes.indexOf(preselectedLang) > -1 && !vm.newTranslation.text) {
                vm.newTranslation.lang = preselectedLang;
            } else if (!vm.newTranslation.lang) {
                vm.newTranslation.lang = vm.showAutoDetect ? 'auto' : langCodes[0];
            }
            focusInput('#translation-form-' + id);
        }

        function saveTranslation(sentenceId) {
            $cookies.put('translationLang', vm.newTranslation.lang);

            if (vm.newTranslation && vm.newTranslation.text) {
                vm.inProgress = true;
                if (vm.newTranslation.editable) {
                    saveSentence(vm.newTranslation).then(function(result) {
                        show('translations');
                        vm.inProgress = false;
                        vm.newTranslation = {};
                    });
                } else {
                    var data = {
                        id: sentenceId,
                        selectLang: vm.newTranslation.lang,
                        value: vm.newTranslation.text
                    };
                    $http.post(rootUrl + '/sentences/save_translation', data).then(function(result) {
                        var translation = result.data.result;
                        translation.editable = true;
                        translation.parentId = sentenceId;
                        allDirectTranslations.unshift(translation);
                        vm.newTranslation = {};
                        show('translations');
                        vm.inProgress = false;
                        showFewerTranslations();
                    });
                }
            }
        }

        function editTranslation(translation) {
            show('translation_form');
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
            show('sentence_form');
            focusInput('#sentence-form-' + vm.sentence.id);
        }
        
        function cancelEdit() {
            hide('sentence_form');
            initSentence(oldSentence);
        }

        function editSentence() {
            vm.inProgress = true;

            saveSentence(vm.sentence).then(function(result) {
                hide('sentence_form');
                vm.inProgress = false;
                initSentence(result.data.result);
            });
        }

        function saveSentence(sentence) {
            var lang = sentence.lang === 'unknown' ? '' : sentence.lang;
            var data = {
                id: [lang, sentence.id].join('_'),
                value: sentence.text
            };
            return $http.post(rootUrl + '/sentences/edit_sentence', data);
        }

        function initSentence(data) {
            data.lang = data.lang ? data.lang : 'unknown';
            oldSentence = data;
            vm.sentence = angular.copy(data);
        }

        function favorite() {
            var action = vm.sentence.is_favorite ? 'remove_favorite' : 'add_favorite';
            
            $http.get(rootUrl + '/favorites/' + action + '/' + vm.sentence.id).then(function(result) {
                vm.sentence.is_favorite = !vm.sentence.is_favorite;
            });
        }

        function adopt() {
            var action = vm.sentence.is_owned_by_current_user ? 'let_go' : 'adopt';
            
            $http.get(rootUrl + '/sentences/' + action + '/' + vm.sentence.id).then(function(result) {
                vm.sentence.user = result.data.user;
                vm.sentence.is_owned_by_current_user = vm.sentence.user && vm.sentence.user.username;
            });
        }

        function list() {
            initLists(vm.sentence.sentences_lists);

            if (vm.visibility['list_form']) {
                closeList();
            } else {
                show('list_form');
                focusInput('#list-form-' + vm.sentence.id);
                resetLists();
            }
        }

        function closeList() {
            vm.hide('list_form');
            vm.listSearch = '';
        }

        function toggleList(list) {
            vm.inProgress = true;
            var action = list.hasSentence ? 'add_sentence_to_list' : 'remove_sentence_from_list';

            $http.get(rootUrl + '/sentences_lists/' + action + '/' + vm.sentence.id + '/' + list.id).then(function(result) {
                vm.inProgress = false;
                $cookies.put('most_recent_list', list.id);
            });
        }

        function addToNewList() {
            if (!vm.listSearch) {
                return;
            }
            
            vm.inProgress = true;
            var data = { 
                name: vm.listSearch,
                sentenceId: vm.sentence.id
            };
            $http.post(rootUrl + '/sentences_lists/add_sentence_to_new_list', data).then(function(result) {
                vm.inProgress = false;
                vm.listSearch = '';
                $rootScope.$broadcast('newListCreated', result.data.result, vm.sentence.id);
            });
        }

        function moveRecentListToTop() {
            var i = allLists.findIndex(function(item) {
                return item.id === parseInt($cookies.get('most_recent_list'));
            });
            if (lastSelectedList) {
                lastSelectedList.isLastSelected = false;
            }
            if (i > -1) {
                lastSelectedList = allLists[i];
                lastSelectedList.isLastSelected = true;
                allLists.splice(i, 1);
                allLists.unshift(lastSelectedList);
            }
        }

        function show(name) {
            for (var i in vm.visibility) {
                vm.visibility[i] = false;
            }
            vm.visibility[name] = true;
        }

        function hide(name) {
            vm.visibility[name] = false;
            vm.visibility['translations'] = true;
        }

        function searchList() {
            if (timeout) {
                $timeout.cancel(timeout);
            }
            timeout = $timeout(function() {
                if (!vm.listSearch) {
                    resetLists();
                } else {
                    vm.lists = allLists.filter(function(item) {
                        var regexp = new RegExp(vm.listSearch, 'gi');
                        return item.name.match(regexp);
                    });
                    vm.listType = 'search';
                }
            }, 500);
        }

        function resetLists() {
            moveRecentListToTop();
            vm.lists = allLists.filter(function(item) {
                return item.is_mine === '1' || item.isLastSelected;
            }).slice(0, 10);
            vm.listType = 'of_user';
        }
    }

})();