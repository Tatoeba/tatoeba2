(function() {
    'use strict';

    angular
        .module('app')
        .controller('RandomSentenceController', RandomSentenceController);

    function RandomSentenceController($rootScope, $cookies) {
        var vm = this;

        vm.lang = null;
        vm.showNewDesignAnnouncement = false;

        vm.init = init;
        vm.showAnother = showAnother;
        vm.hideAnnouncement = hideAnnouncement;

        ///////////////////////////////////////////////////////////////////////////

        function init() {
            vm.lang = $cookies.get('random_lang_selected');
            if (!vm.lang) {
                vm.lang = 'und';
            }

            vm.showNewDesignAnnouncement = !$cookies.get('hide_new_design_announcement');
        }

        function showAnother(lang) {
            $cookies.put('random_lang_selected', lang);
            $rootScope.$broadcast('randomSentenceRequested', lang);
        }

        function hideAnnouncement() {
            $cookies.put('hide_new_design_announcement', true);
            vm.showNewDesignAnnouncement = false;
        }
    }

})();
