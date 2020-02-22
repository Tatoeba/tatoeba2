(function() {
    'use strict';

    angular
        .module('app')
        .controller('RandomSentenceController', RandomSentenceController);

    function RandomSentenceController($rootScope) {
        var vm = this;

        vm.showAnother = showAnother;

        ///////////////////////////////////////////////////////////////////////////

        function showAnother() {
            $rootScope.$broadcast('randomSentenceRequested');
        }
    }

})();
