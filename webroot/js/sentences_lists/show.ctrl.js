(function() {
    'use strict';

    angular
        .module('app')
        .controller('SentencesListsShowController', SentencesListsShowController);

    function SentencesListsShowController($cookies, $http) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;

        vm.list = {};
        vm.userLanguages = [];
        vm.showAutoDetect = false;
        vm.licenses = [];
        vm.newSentence = {};
        vm.sentences = [];
        vm.inProgress = false;
        vm.showForm = false;

        vm.init = init;
        vm.initList = initList;
        vm.addSentence = addSentence;

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
            vm.newSentence.license = Object.keys(vm.licenses)[0];
        }

        function initList(listId) {
            vm.list = { id: listId };
        }

        function addSentence() {
            vm.inProgress = true;
            var data = {
                'selectedLang': vm.newSentence.lang,
                'value': vm.newSentence.text,
                'sentenceLicense': vm.newSentence.license
            };
            $http.post(rootUrl + '/sentences/add_an_other_sentence', data).then(function(result) {
                var sentence = result.data.sentence;
                $http.get(rootUrl + '/sentences_lists/add_sentence_to_list/' + sentence.id + '/' + vm.list.id).then(function() {
                    $cookies.put('contribute_lang', vm.newSentence.lang);
                    vm.newSentence.text = '';
                    vm.sentences.unshift(sentence);
                    vm.inProgress = false;
                });
            });
        }
    }

})();
