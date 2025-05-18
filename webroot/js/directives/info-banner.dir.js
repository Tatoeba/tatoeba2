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

    InfoBannerController.$inject = ['$cookies', '$http', '$element'];
    function InfoBannerController($cookies, $http, $element) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;
        var cookieName = null;
        var expireDate =  new Date();

        vm.init = init;
        vm.hideAnnouncement = hideAnnouncement;

        ///////////////////////////////////////////////////////////////////////////

        function init(cookie, secsToExpiration = 3600 * 24 * 7) {
            cookieName = cookie;
            if ($cookies.get(cookieName)) {
                $element.remove();
            }
            expireDate.setTime(expireDate.getTime() + secsToExpiration * 1000);
        }

        function hideAnnouncement(saveInSettings) {
            $element.remove();

            if (saveInSettings) {
                var data = {};
                data[cookieName] = 1;
                $http.post(rootUrl + '/user/save_banner_setting', data);
            } else {
                $cookies.put(cookieName, true, {'expires': expireDate});
            }
        }
    }
})();