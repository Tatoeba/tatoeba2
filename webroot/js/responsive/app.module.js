(function() {
    'use strict';

    // non-jquery function param() from
    // https://blog.garstasio.com/you-dont-need-jquery/ajax/#url-encoding
    function param(object) {
        var encodedString = '';
        for (var prop in object) {
            if (object.hasOwnProperty(prop)) {
                if (encodedString.length > 0) {
                    encodedString += '&';
                }
                encodedString += encodeURI(prop + '=' + object[prop]);
            }
        }
        return encodedString;
    }

    angular
        .module('app', ['ngMaterial', 'ngMessages'])
        .config(['$mdThemingProvider', '$mdIconProvider', '$httpProvider', function($mdThemingProvider, $mdIconProvider, $httpProvider) {
            $mdThemingProvider.theme('default')
                .primaryPalette('green')
                .accentPalette('grey')
                .warnPalette('red',  {'default': '700'});
            $httpProvider.defaults.transformRequest = function(data) {
                if (data === undefined) {
                    return data;
                }
                return param(data);
            };
            $httpProvider.defaults.headers.post['Content-Type'] =
                'application/x-www-form-urlencoded; charset=UTF-8';
            $httpProvider.defaults.headers.common['X-Requested-With'] =
                'XMLHttpRequest';
        }]);
})();