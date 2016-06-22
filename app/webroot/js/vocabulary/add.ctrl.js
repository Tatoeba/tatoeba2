(function() {
    'use strict';

    angular
        .module('app')
        .controller('VocabularyAddController', ['$http', VocabularyAddController]);

    function VocabularyAddController($http) {
        var vm = this;

        vm.data = {};
        vm.vocabularyAdded = [];

        vm.add = add;
        vm.remove = remove;
        vm.isAdding = false;

        ///////////////////////////////////////////////////////////////////////////

        function add() {
            vm.isAdding = true;
            $http.post('/vocabulary/save', vm.data).then(
                function(response) {
                    var data = response.data;
                    var query = encodeURIComponent('="' + data.text + '"');
                    data.url = '/sentences/search?' +
                        'query=' + query + '&' +
                        'from=' + data.lang;

                    vm.vocabularyAdded.unshift(data);
                    vm.data.text = '';
                    vm.isAdding = false;
                }
            );
        }

        function remove(id) {
            $http.post('/vocabulary/remove/' + id).then(
                function(response) {
                    $('#vocabulary_' + id).hide();
                }
            );
        }
    }
})();