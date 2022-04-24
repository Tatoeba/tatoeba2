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
        vm.saveAudio = saveAudio;
        vm.deleteAudio = deleteAudio;

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

        function saveAudio(audio) {
            var data = {
                author: audio.author,
                enabled: audio.enabled
            };
            var options = {
                headers: {'Content-Type': 'application/json;charset=utf-8'},
                transformRequest: angular.toJson
            };
            $http.post(rootUrl + '/audio/save/' + audio.id, data, options).then(
                function success(result) {
                    window.location.reload();
                },
                function error(result) {
                }
            );
        }

        function deleteAudio(audio, confirmMessage) {
            if (confirm(confirmMessage)) {
                $http.post(rootUrl + '/audio/delete/' + audio.id).then(
                    function success(result) {
                        window.location.reload();
                    },
                    function error(result) {
                    }
                );
            }
        }
    }
})();
