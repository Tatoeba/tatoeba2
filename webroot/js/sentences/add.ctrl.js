(function() {
    'use strict';

    angular
        .module('app')
        .controller('SentencesAddController', SentencesAddController);

    function SentencesAddController($cookies, $http) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;

        vm.userLanguages = [];
        vm.showAutoDetect = false;
        vm.licenses = [];
        vm.newSentence = {};
        vm.sentences = [];
        vm.inProgress = false;

        vm.init = init;
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
        }

        function addSentence() {
            if (!vm.newSentence.text) {
                return;
            }
            
            vm.inProgress = true;
            var data = {
                'selectedLang': vm.newSentence.lang,
                'value': vm.newSentence.text,
                'sentenceLicense': vm.newSentence.license
            };
            $http.post(rootUrl + '/sentences/add_an_other_sentence', data).then(function(result) {
                var sentence = result.data.sentence;
                sentence.duplicate = result.data.duplicate;

                $cookies.put('contribute_lang', vm.newSentence.lang);
                vm.newSentence.text = '';
                vm.sentences.unshift(result.data.sentence);
                vm.inProgress = false;
            });
        }
    }

})();
