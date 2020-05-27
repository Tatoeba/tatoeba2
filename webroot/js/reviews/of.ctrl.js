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
        .controller('ReviewsOfController', ['$scope', '$http', ReviewsOfController])
        .directive('iconWithProgress', function() {
            return {
                restrict: 'E',
                transclude: true,
                scope: {
                    isLoading: '=',
                },
                template:
                    '<span ng-if="!isLoading"><ng-transclude></ng-transclude></span>' +
                    '<md-button class="md-icon-button" ng-if="isLoading">' +
                        '<md-progress-circular md-diameter="24"></md-progress-circular>' +
                    '</md-button>'
            }
        });

    function ReviewsOfController($scope, $http) {
        const rootUrl = get_tatoeba_root_url();

        var vm = this;

        vm.initSentence = initSentence;
        vm.iconsInProgress = {};
        vm.sentence = null;
        vm.correctness = null;
        vm.setReview = setReview;
        vm.resetReview = resetReview;

        ///////////////////////////////////////////////////////////////////////////

        function initSentence(sentenceData, correctness) {
            vm.sentence = angular.copy(sentenceData);
            vm.correctness = correctness;
        }

        function setReview(value) {
            var reviewType = getReviewType(value);
            vm.iconsInProgress[reviewType] = true;
            $http.get(rootUrl + '/reviews/add_sentence/' + vm.sentence.id + '/' + value).then(function(response) {
                vm.correctness = parseInt(response.data.result.correctness);
                vm.iconsInProgress[reviewType] = false;
            });
        }

        function resetReview() {
            var reviewType = getReviewType(vm.correctness);
            vm.iconsInProgress[reviewType] = true;
            $http.get(rootUrl + '/reviews/delete_sentence/' + vm.sentence.id).then(function(response) {
                if (response.data.result) {
                    vm.correctness = null;
                    vm.iconsInProgress[reviewType] = false;
                }
            });
        }

        function getReviewType(correctness) {
            if (correctness === 1) {
                return 'reviewOk';
            } else if (correctness === 0) {
                return 'reviewUnsure';
            } else if (correctness === -1) {
                return 'reviewNotOk';
            }
            return null;
        }
    }
})();
