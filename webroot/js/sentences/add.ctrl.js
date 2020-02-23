(function() {
    'use strict';

    angular
        .module('app')
        .controller('SentencesAddController', SentencesAddController);

    function SentencesAddController($cookies, $http) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;

        vm.userLanguages = [];
        vm.licenses = [];
        vm.newSentence = {};
        vm.sentences = [];
        vm.inProgress = false;

        vm.init = init;
        vm.addSentence = addSentence;

        ///////////////////////////////////////////////////////////////////////////

        function init(userLanguages, licenses) {
            vm.userLanguages = userLanguages;
            if (userLanguages.length > 1) {
                vm.newSentence.lang = $cookies.get('contribute_lang') ? $cookies.get('contribute_lang') : 'auto';
            } else {
                vm.newSentence.lang = Object.keys(vm.userLanguages)[0];
            }

            vm.licenses = licenses;
            vm.newSentence.license = Object.keys(vm.licenses)[0];
        }

        function addSentence() {
            vm.inProgress = true;
            var data = {
                'selectedLang': vm.newSentence.lang,
                'value': vm.newSentence.text,
                'sentenceLicense': vm.newSentence.license
            };
            $http.post(rootUrl + '/sentences/add_an_other_sentence', data).then(function(result) {
                vm.newSentence.text = '';
                vm.sentences.unshift(result.data.sentence);
                vm.inProgress = false;
            });
        }
    }

})();
