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
            'sentenceAndTranslations',
            sentenceAndTranslations
        );

    function sentenceAndTranslations() {
        return {
            scope: true,
            controller: SentenceAndTranslationsController,
            controllerAs: 'vm'
        };
    }

    function SentenceAndTranslationsController() {
        var vm = this;

        vm.isExpanded = false;
        vm.expandableIcon = 'expand_more';

        vm.expandOrCollapse = expandOrCollapse;
        vm.playAudio = playAudio;

        /////////////////////////////////////////////////////////////////////////

        function expandOrCollapse() {
            vm.isExpanded = !vm.isExpanded;

            if (vm.isExpanded) {
                vm.expandableIcon = 'expand_less';
            } else {
                vm.expandableIcon = 'expand_more';
            }
        }

        function playAudio(audioURL) {
            var audio = new Audio(audioURL);
            audio.play();
        }
    }

})();