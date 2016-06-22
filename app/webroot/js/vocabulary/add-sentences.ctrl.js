(function() {
    'use strict';

    angular
        .module('app')
        .controller('VocabularyAddSentencesController', [
            '$http', VocabularyAddSentencesController
        ]);

    function VocabularyAddSentencesController($http) {
        var vm = this;

        vm.showForm = showForm;
        vm.hideForm = hideForm;
        vm.saveSentence = saveSentence;

        vm.sentencesAdded = {};

        ///////////////////////////////////////////////////////////////////////////

        function showForm(id) {
            $('#form_' + id).removeClass('ng-hide');
            $('#form_' + id + ' input').focus();
        }

        function hideForm(id) {
            $('#form_' + id).addClass('ng-hide');
        }

        function saveSentence(id, lang) {
            var loader = $('#loader_' + id);
            var body = {
                'text': vm.sentence[id],
                'lang': lang
            };

            if (!vm.sentencesAdded[id]) {
                vm.sentencesAdded[id] = [];
            }

            loader.removeClass('ng-hide');
            hideForm(id);

            $http.post('/vocabulary/save_sentence/' + id, body).then(
                function(response) {
                    loader.addClass('ng-hide');

                    vm.sentencesAdded[id].push(response.data);
                    vm.sentence[id] = null;
                }
            );
        }

    }
})();