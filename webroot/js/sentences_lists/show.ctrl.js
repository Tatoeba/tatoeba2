(function() {
    'use strict';

    angular
        .module('app')
        .controller('SentencesListsShowController', SentencesListsShowController);

    function SentencesListsShowController($scope, $cookies, $http) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;
        var sentenceForm;

        vm.list = {};
        vm.userLanguages = [];
        vm.showAutoDetect = false;
        vm.licenses = [];
        vm.newSentence = {};
        vm.sentences = [];
        vm.inProgress = false;
        vm.showForm = false;
        vm.isRemoved = {};
        vm.showEditNameForm = false;

        vm.init = init;
        vm.initList = initList;
        vm.addSentence = addSentence;
        vm.removeSentence = removeSentence;
        vm.undoRemoval = undoRemoval;
        vm.editName = editName;
        vm.saveListName = saveListName;

        ///////////////////////////////////////////////////////////////////////////

        $scope.$watch(angular.bind(this, function () {
            return vm.newSentence.text;
          }), function () {
            if (sentenceForm) {
                vm.newSentence.error = null;
            }
          });

        ///////////////////////////////////////////////////////////////////////////

        function init(userLanguages, licenses) {
            var langCodes = Object.keys(userLanguages);
            var preselectedLang = $cookies.get('contribute_lang');
            vm.userLanguages = userLanguages;
            vm.showAutoDetect = langCodes.length > 1;
            if (langCodes.indexOf(preselectedLang) > -1) {
                vm.newSentence.lang = preselectedLang;
            } else {
                vm.newSentence.lang = vm.showAutoDetect ? 'auto' : langCodes[0];
            }
            vm.licenses = licenses;
        }

        function initList(list) {
            vm.list = list;
            vm.list.currentName = list.name;
        }

        function addSentence(form) {
            if (!vm.newSentence.text) {
                return;
            }
            
            sentenceForm = form;
            vm.inProgress = true;
            var data = {
                'selectedLang': vm.newSentence.lang,
                'value': vm.newSentence.text,
                'sentenceLicense': vm.newSentence.license
            };
            $http.post(rootUrl + '/sentences/add_an_other_sentence', data).then(function(result) {
                var sentence = result.data.sentence;
                sentence.duplicate = result.data.duplicate;
                $http.get(rootUrl + '/sentences_lists/add_sentence_to_list/' + sentence.id + '/' + vm.list.id).then(function(result) {
                    var result = result.data;
                    if (parseInt(result.result) === vm.list.id) {
                        sentence.sentences_lists.push({id: vm.list.id});
                        $cookies.put('contribute_lang', vm.newSentence.lang);
                        vm.newSentence.text = '';
                        vm.sentences.unshift(sentence);
                    } else if (result.error) {
                        vm.newSentence.error = result.error;
                    }
                    vm.inProgress = false;
                });
            });
        }

        function removeSentence(sentenceId) {
            $http.get(rootUrl + '/sentences_lists/remove_sentence_from_list/' + sentenceId + '/' + vm.list.id).then(function(result) {
                vm.isRemoved[sentenceId] = true;
            });
        }

        function undoRemoval(sentenceId) {
            $http.get(rootUrl + '/sentences_lists/add_sentence_to_list/' + sentenceId + '/' + vm.list.id).then(function() {
                vm.isRemoved[sentenceId] = false;
            });
        }

        function editName() {
            vm.showEditNameForm =  true;
            setTimeout(function() {
                var input = angular.element(document.querySelector('#edit-name-input'));
                input.focus();
            }, 100);
        }

        function saveListName() {
            if (vm.list.currentName === vm.list.name) {
                return;
            }
            
            $http.post(rootUrl + '/sentences_lists/save_name', vm.list).then(function(result) {
                vm.list.currentName = result.data.result;
                vm.showEditNameForm = false;
            });
        }
    }

})();
