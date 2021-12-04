(function() {
    'use strict';

    angular
        .module('app')
        .controller('AudioDetailsController', ['$http', AudioDetailsController]);

    function AudioDetailsController($http) {
        const rootUrl = get_tatoeba_root_url();
        var vm = this;

        vm.init = init;
        vm.getLicenseName = getLicenseName;
        vm.getLicenseLink = getLicenseLink;
        vm.editAudio = editAudio;

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

        function editAudio() {
            var data = {};
            vm.audios.forEach(function(audio) {
                data[audio.id] = {
                    author: audio.author,
                    enabled: audio.enabled,
                };
            });
            $http.post(rootUrl + '/audio/mass_edit', data).then(
                function success(result) {
                    window.location.reload();
                },
                function error(result) {
                }
            );
        }
    }
})();
