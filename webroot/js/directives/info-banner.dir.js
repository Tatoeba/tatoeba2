(function() {
    'use strict';

    angular
        .module('app')
        .directive('infoBanner', function() {
            return {
                scope: true,
                controller: InfoBannerController,
                controllerAs: 'vm'
            };
        });

    InfoBannerController.$inject = ['$cookies'];
    function InfoBannerController($cookies) {
        var vm = this;
        var cookieName = null;

        vm.isInfoBannerVisible = false;

        vm.init = init;
        vm.hideAnnouncement = hideAnnouncement;

        ///////////////////////////////////////////////////////////////////////////

        function init(cookie) {
            cookieName = cookie;
            vm.isInfoBannerVisible = !$cookies.get(cookieName);
        }

        function hideAnnouncement() {
            $cookies.put(cookieName, true);
            vm.isInfoBannerVisible = false;
        }
    }
})();