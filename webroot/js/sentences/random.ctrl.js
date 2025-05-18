(function() {
    'use strict';

    angular
        .module('app')
        .controller('RandomSentenceController', RandomSentenceController);

    function RandomSentenceController($rootScope, $cookies) {
        var vm = this;
        
        vm.lang = null;

        vm.init = init;
        vm.showAnother = showAnother;

        ///////////////////////////////////////////////////////////////////////////

        function init() {
            vm.lang = $cookies.get('random_lang_selected');
            if (!vm.lang) {
                vm.lang = 'und';
            }
        }

        function showAnother(lang) {
            $cookies.put('random_lang_selected', lang);
            $rootScope.$broadcast('randomSentenceRequested', lang);
        }
    }

})();
