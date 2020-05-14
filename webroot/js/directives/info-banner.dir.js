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

    InfoBannerController.$inject = ['$cookies', '$http'];
    function InfoBannerController($cookies, $http) {
        const rootUrl = get_tatoeba_root_url();

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

        function hideAnnouncement(saveInSettings) {
            vm.isInfoBannerVisible = false;

            if (saveInSettings) {
                var data = {};
                data[cookieName] = 1;
                $http.post(rootUrl + '/user/save_banner_setting', data);
            } else {
                $cookies.put(cookieName, true);
            }
        }
    }
})();