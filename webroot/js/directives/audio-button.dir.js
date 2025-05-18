/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

(function() {
    'use strict';

    angular
        .module('app')
        .directive(
            'audioButton',
            audioButton
        );

    function audioButton() {
        return {
            scope: {
                audios: '<',
            },
            templateUrl: 'audio-button-template',
            controllerAs: 'vm',
            controller: ['$scope', function($scope) {
                var vm = this;
                var lastPlayedAudioIndex;

                const rootUrl = get_tatoeba_root_url();

                vm.playAudio = playAudio;
                vm.getAudioUrl = getAudioUrl;
                vm.getAudioAuthor = getAudioAuthor;

                /////////////////////////////////////////////////////////////////////////

                function getNextPlayAudioIndex(audios) {
                    if (lastPlayedAudioIndex === undefined) {
                        lastPlayedAudioIndex = getRandomInt(0, audios.length);
                    }
                    if (audios.length == 0) {
                        return undefined;
                    } else {
                        return (lastPlayedAudioIndex + 1) % audios.length;
                    }
                }
        
                function getAudioUrl(audios, playIndex = undefined) {
                    playIndex = (typeof playIndex !== 'undefined') ? playIndex : getNextPlayAudioIndex(audios);
                    return rootUrl + '/audio/download/' + audios[playIndex].id;
                }

                function playAudio(audios) {
                    var playIndex = getNextPlayAudioIndex(audios);
                    if (playIndex !== undefined) {
                        var audioURL = getAudioUrl(audios, playIndex);
                        var audio = new Audio(audioURL);
                        audio.play();
                        lastPlayedAudioIndex = playIndex;
                    }
                }
        
                function getAudioAuthor(audios) {
                    var playIndex = getNextPlayAudioIndex(audios);
                    if (playIndex === undefined) {
                        return false;
                    } else {
                        return audios[playIndex].author;
                    }
                }

                function getRandomInt(min, max) {
                    return Math.floor(Math.random() * (max - min + 1)) + min;
                }
            }]
        };
    }
})();
