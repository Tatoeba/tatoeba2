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
                    lang: '<',
                    title: '<'
                },
                link: function($scope) {
                    if (!$scope.lang) {
                        $scope.lang = 'unknown';
                    }
                },
                template: 
                    '<img class="language-icon" width="30" height="20" ' +
                      'ng-attr-title="{{title ? title : lang}}"' +
                      'ng-src="/img/flags/{{lang}}.svg" />'
            }
        })
        .directive('toggleAllMenus', ['$rootScope', '$cookies', function($rootScope, $cookies) {
            return {
                restrict: 'A',
                controllerAs: 'menu',
                controller: function() {
                    var vm = this;

                    vm.expanded = $cookies.get('sentence_menu_expanded') === 'true';
                    vm.toggleAll = toggleAll;

                    /////////////////////////////////////////////////////////////////////////

                    function toggleAll() {
                        vm.expanded = !vm.expanded;
                        $rootScope.$broadcast('menuToggled', vm.expanded);
                        $cookies.put('sentence_menu_expanded', vm.expanded);
                    }
                }
            }
        }]);

    angular.module('app').requires.push('ngclipboard');

    function sentenceAndTranslations() {
        return {
            scope: true,
            controller: SentenceAndTranslationsController,
            controllerAs: 'vm'
        };
    }

    SentenceAndTranslationsController.$inject = ['$rootScope', '$scope', '$http', '$cookies', '$timeout', '$injector'];
    function SentenceAndTranslationsController($rootScope, $scope, $http, $cookies, $timeout, $injector) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;
        var oldSentence = null;
        var allLists = [];
        var lastSelectedList = null;
        var timeout;
        var listsDataService;
        var editableTranslations = [];
        var duplicateTranslations = [];
        var newTranslations = [];
        var translationLang;

        vm.menu = {};
        vm.inProgress = false;
        vm.iconsInProgress = {};
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
        vm.hasHiddenTranscriptions = false;

        vm.init = init;
        vm.initMenu = initMenu;
        vm.initLists = initLists;
        vm.expandOrCollapse = expandOrCollapse;
        vm.toggleMenu = toggleMenu;
        vm.toggleTranscriptions = toggleTranscriptions;
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
        vm.saveTranscription = saveTranscription;
        vm.saveLink = saveLink;

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
                if (!result.data || !result.data.sentence) {
                    // TODO Show error
                    return;
                }

                var sentence = result.data.sentence;
                var directTranslations = sentence.translations[0];
                var indirectTranslations = sentence.translations[1];
                init(vm.userLanguages, sentence, directTranslations, indirectTranslations);
                initMenu(false, sentence.permissions);
                if (sentence.permissions) {
                    initLists(sentence.sentences_lists);
                }
            }).finally(function() {
                vm.inProgress = false;
                vm.isExpanded = false;
                vm.expandableIcon = 'expand_more';
            });
        });

        $scope.$on('menuToggled', function(event, isMenuExpanded) {
            vm.isMenuExpanded = isMenuExpanded;
        });

        /////////////////////////////////////////////////////////////////////////

        function init(langs, sentence, directTranslations, indirectTranslations, translationLanguage) {
            editableTranslations = [];
            duplicateTranslations = [];
            newTranslations = [];
            vm.newTranslation = {};
            vm.hasHiddenTranscriptions = false;

            vm.userLanguages = langs;
            vm.showAutoDetect = Object.keys(langs).length > 1;
            initSentence(sentence);
            vm.directTranslations = directTranslations ? directTranslations : [];
            vm.directTranslations.forEach(function(translation) {
                initTranscriptions(translation);
            });
            vm.indirectTranslations = indirectTranslations ? indirectTranslations : [];
            vm.indirectTranslations.forEach(function(translation) {
                initTranscriptions(translation);
            });
            updateTranslationsVisibility();
            translationLang = translationLanguage ? translationLanguage : 'und';
        }

        function initMenu(isExpanded, menu) {
            vm.iconsInProgress = {};
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
            var selectableLists = angular.copy(listsDataService.getLists());

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

        function expandOrCollapse(isExpanded) {
            if (isExpanded) {
                expandTranslations();
            } else {
                collapseTranslations();
            }
        }

        function collapseTranslations() {
            vm.isExpanded = false;
            vm.expandableIcon = 'expand_more';
            updateTranslationsVisibility();
        }

        function expandTranslations() {
            vm.isExpanded = true;
            vm.expandableIcon = 'expand_less';
            updateTranslationsVisibility();
        }

        function updateTranslationsVisibility() {
            vm.directTranslations.forEach(function(item, index) {
                item.isHidden = !vm.isExpanded && index >= vm.sentence.max_visible_translations;
            });
            vm.indirectTranslations.forEach(function(item, index) {
                item.isHidden = !vm.isExpanded
                    && vm.directTranslations.length + index >= vm.sentence.max_visible_translations;
            });
        }

        function toggleMenu() {
            vm.isMenuExpanded = !vm.isMenuExpanded;
        }

        function toggleTranscriptions() {
            toggleMenu();
            expandOrCollapse(vm.isMenuExpanded);
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
            focusInput('translation-form-' + id);
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
                    var url = rootUrl + '/sentences/save_translation?translationLang=' + translationLang + '&numberOfTranslations=' + getNumberOfTranslations();
                    $http.post(url, data).then(function(result) {
                        var translation = result.data.translation;
                        var sentence = result.data.sentence;
                        initSentence(sentence);
                        if (translationLang === 'und') {
                            updateNewTranslationsInfo(translation, sentenceId, sentence.translations);
                        } else {
                            vm.directTranslations.unshift(translation);
                        }
                        refreshTranslations();
                        vm.newTranslation = {};
                        show('translations');
                        vm.inProgress = false;
                    });
                }
            }
        }

        function editTranslation(translation) {
            show('translation_form');
            vm.newTranslation = translation;
            focusInput('translation-form-' + translation.parentId);
        }

        function focusInput(id) {
            setTimeout(function() {
                var input = document.getElementById(id);
                input.focus();
            }, 100);
        }

        function edit() {
            show('sentence_form');
            focusInput('sentence-form-' + vm.sentence.id);
        }
        
        function cancelEdit() {
            hide('sentence_form');
            initSentence(oldSentence);
        }

        function editSentence() {
            var sentenceChanged = oldSentence.text !== vm.sentence.text || oldSentence.lang !== vm.sentence.lang;
            var transcriptionChanged = false;
            if (vm.sentence.transcriptions.length > 0) {
                var oldTranscription = getEditableTranscription(oldSentence);
                var transcription = getEditableTranscription(vm.sentence);
                var oldMarkup = oldTranscription ? oldTranscription.markup : null;
                var newMarkup = transcription ? transcription.markup : null,
                transcriptionChanged = oldMarkup !== newMarkup;
            }
            
            if (sentenceChanged) {
                vm.inProgress = true;
                saveSentence(vm.sentence).then(function(result) {
                    if (transcriptionChanged) {
                        saveTranscription(transcription, result.data.result, 'save');
                    } else {
                        initSentence(result.data.result);
                        hide('sentence_form');
                        vm.inProgress = false;
                    }
                });
            } else if (transcriptionChanged) {
                saveTranscription(transcription, vm.sentence, 'save');
            }
        }

        function saveSentence(sentence) {
            var data = {
                id: sentence.id,
                lang: sentence.lang === 'unknown' ? '' : sentence.lang,
                text: sentence.text
            };
            return $http.post(rootUrl + '/sentences/edit_sentence', data);
        }

        function initSentence(data) {
            data.lang = data.lang ? data.lang : 'unknown';
            oldSentence = data;
            vm.sentence = angular.copy(data);
            initTranscriptions(vm.sentence);
        }

        function initTranscriptions(sentence) {
            var transcriptions = sentence.transcriptions;
            if (sentence.lang === 'jpn' && transcriptions) {
                sentence.furigana = transcriptions.find(function(item) {
                    return !item.needsReview && item.type === 'altscript';
                });
            }
            transcriptions.forEach(function(item) {
                if (item.needsReview) {
                    vm.hasHiddenTranscriptions = true;
                }
            });
        }

        function favorite() {
            var action = vm.sentence.is_favorite ? 'remove_favorite' : 'add_favorite';
            vm.iconsInProgress.favorite = true;
            $http.get(rootUrl + '/favorites/' + action + '/' + vm.sentence.id).then(function(result) {
                vm.sentence.is_favorite = !vm.sentence.is_favorite;
                vm.iconsInProgress.favorite = false;
            });
        }

        function adopt() {
            var action = vm.sentence.is_owned_by_current_user ? 'let_go' : 'adopt';
            vm.iconsInProgress.adopt = true;
            $http.get(rootUrl + '/sentences/' + action + '/' + vm.sentence.id).then(function(result) {
                var sentence = result.data.sentence;
                sentence.expandLabel = vm.sentence.expandLabel;
                initSentence(sentence);
                initMenu(!vm.isExpanded, sentence.permissions);
                vm.iconsInProgress.adopt = false;
            });
        }

        function list() {
            initLists(vm.sentence.sentences_lists);

            if (vm.visibility['list_form']) {
                closeList();
            } else {
                show('list_form');
                focusInput('list-form-' + vm.sentence.id);
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
                updateSentenceLists(action, list, result);
                $cookies.put('most_recent_list', list.id);
            });
        }

        function updateSentenceLists(action, list, result) {
            var result = result.data;
            if (action === 'add_sentence_to_list') {
                var listId = parseInt(result.result);
                if (listId === list.id) {
                    vm.sentence.sentences_lists.push({id: list.id});
                }
            } else {
                var removed = result.removed;
                if (removed) {
                    var index = vm.sentence.sentences_lists.findIndex(function(item) {
                        return item.id === list.id;
                    });
                    vm.sentence.sentences_lists.splice(index, 1);
                }
            }
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
            vm.listSearch = '';
            vm.listType = 'of_user';
        }

        function saveTranscription(transcription, sentence, action) {
            var lang = sentence.lang + '-' + transcription.script;
            var text = transcription.markup;
            var url = rootUrl + '/transcriptions/' + action + '/' + sentence.id + '/' + transcription.script;
            var data = {
                value: markupToStored(lang, text)
            };
            var i = sentence.transcriptions.findIndex(function(item) {
                return item.id === transcription.id;
            });

            vm.inProgress = true;
            $http.post(url, data).then(function(result) {
                transcription = result.data.result;
                hide('sentence_form');
            }, function(error) {
                if (error.data) {
                    if (error.status === 403) {
                        transcription.errors = [error.data];
                    } else {
                        transcription.errors = error.data;
                    }
                } else {
                    transcription.errors = [error.statusText];
                }
            }).finally(function() {
                sentence.transcriptions.splice(i, 1);
                sentence.transcriptions.push(transcription);
                initTranscriptions(sentence);
                vm.inProgress = false;
            });
        }

        function markupToStored(lang, text) {
            if (lang === 'jpn-Hrkt') {
                // Converts the kanji｛reading｝ notation into [kanji|reading]
                var hiragana = 'ぁ-ゖーゝゞ'; // \p{Hiragana}
                var katakana = 'ァ-ヺーヽヾ'; // \p{Katakana}
                var punct = '　-〄〇-〠・'; // 。、「」etc.
                punct += '！＂＃＇（），．／：；？［＼］＾｀～｟｠'; // fullwitdh forms
                punct += ' '; // space
                var regex = '([^｝' + hiragana + katakana + punct + ']*)｛([^｝]*)｝';
                text = text.replace(uniRegExp(regex, 'g'), '[$1|$2]');
                text = text.replace(uniRegExp('｜', 'g'),  '|');
            }
            return text;
        }

        function uniRegExp(regex, flags) {
            return new RegExp(escapeUnicodeString(regex), flags);
        }

        function escapeUnicodeChar(c) {
            var code = c.charCodeAt(0);
            if (code >= 128) {
               c = '\\u' + ('0000' + c.charCodeAt(0).toString(16)).slice(-4);
            }
            return c;
        }

        function escapeUnicodeString(input) {
            var output = '';
            for (var i = 0, l = input.length; i < l; i++) {
                output += escapeUnicodeChar(input.charAt(i));
            }
            return output;
        }

        function getEditableTranscription(sentence) {
            return sentence.transcriptions.find(function(item) {
                return item.markup;
            });
        }
        
        function saveLink(action, translation) {
            var url = rootUrl + '/links/' + action + '/' + vm.sentence.id + '/' + translation.id;
            if (translationLang) {
                url += '?translationLang=' + translationLang;
            }
            vm.iconsInProgress['link' + translation.id] = true;
            $http.get(url).then(function(result) {
                var sentence = result.data.sentence;
                initSentence(sentence);
                refreshTranslations(sentence.translations);
                vm.iconsInProgress['link' + translation.id] = false;
            });
        }

        function refreshTranslations(translations) {
            if (translations) {
                vm.directTranslations = translations[0];
                vm.indirectTranslations = translations[1];
            }
            updateTranslationsVisibility();
        }

        function updateNewTranslationsInfo(translation, sentenceId, translations) {
            vm.directTranslations = translations[0];
            vm.indirectTranslations = translations[1];
            
            newTranslations.push(translation.id);
            if (translation.isDuplicate) {
                duplicateTranslations.push(translation.id);
            } else if (translation.is_owned_by_current_user) {
                editableTranslations.push(translation.id);
            }
            vm.directTranslations.forEach(function(item) {
                item.editable = editableTranslations.indexOf(item.id) > -1;
                item.isDuplicate = duplicateTranslations.indexOf(item.id) > -1;;
                item.parentId = sentenceId;
            });
            vm.directTranslations.sort(function(a, b) {
                var indexA = newTranslations.indexOf(a.id);
                var indexB = newTranslations.indexOf(b.id);
                return indexB - indexA;
            });
        }

        function getNumberOfTranslations() {
            return vm.directTranslations.length + vm.indirectTranslations.length;
        }
    }

})();
