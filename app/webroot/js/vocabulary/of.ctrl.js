(function() {
    'use strict';

    angular
        .module('app')
        .controller('VocabularyOfController', ['$http', VocabularyOfController]);

    function VocabularyOfController($http) {
        var vm = this;

        vm.remove = remove;

        ///////////////////////////////////////////////////////////////////////////

        function remove(id) {
            $http.post('/vocabulary/remove/' + id).then(
                function(response) {
                    $('#vocabulary_' + id).hide();
                }
            );
        }
    }
})();