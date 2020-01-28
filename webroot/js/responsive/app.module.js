(function() {
    'use strict';

    angular
        .module('app', ['ngMaterial', 'ngMessages', 'ngCookies', 'ngSanitize'])
        .config(['$mdThemingProvider', '$mdIconProvider', '$httpProvider', function($mdThemingProvider, $mdIconProvider, $httpProvider) {
            $mdThemingProvider.theme('default')
                .primaryPalette('green')
                .accentPalette('grey')
                .warnPalette('red',  {'default': '700'});
            $httpProvider.defaults.transformRequest = function(data) {
                if (data === undefined) {
                    return data;
                }
                return $.param(data);
            };
            $httpProvider.defaults.headers.post['Content-Type'] =
                'application/x-www-form-urlencoded; charset=UTF-8';
            $httpProvider.defaults.headers.common['X-Requested-With'] =
                'XMLHttpRequest';
            $httpProvider.defaults.xsrfHeaderName = 'X-CSRF-Token';
            $httpProvider.defaults.xsrfCookieName = 'csrfToken';
        }])
        .filter('urlEncode', function() {
            return function(input) {
                if (input) {
                    return window.encodeURIComponent(input);
                }
                return "";
            };
        });
})();
