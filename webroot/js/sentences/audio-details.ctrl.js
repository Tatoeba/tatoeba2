(function() {
    'use strict';

    angular
        .module('app')
        .controller('AudioDetailsController', AudioDetailsController);

    function AudioDetailsController($http) {
        var vm = this;

        vm.init = init;
        vm.getLicenseName = getLicenseName;
        vm.getLicenseLink = getLicenseLink;

        ///////////////////////////////////////////////////////////////////////////

        function init(audios, audioLicenses) {
            vm.audios = audios;
            vm.audioLicenses = audioLicenses;
        }

        function getLicenseName(key) {
            key = (key || '');
            if (!(key in vm.audioLicenses)) {
                key = '';
            }
            if (key in vm.audioLicenses && 'name' in vm.audioLicenses[key]) {
                return vm.audioLicenses[key]['name'];
            } else {
                return key;
            }
        }

        function getLicenseLink(key) {
            if (key in vm.audioLicenses && 'url' in vm.audioLicenses[key]) {
                return vm.audioLicenses[key]['url'];
            } else {
                return undefined;
            }
        }
    }
})();
