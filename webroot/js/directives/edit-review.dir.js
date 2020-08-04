/**
* Tatoeba Project, free collaborative creation of languages corpuses project
* Copyright (C) 2020 Tatoeba Project
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
        .directive('editReview', function() {
            return {
                bindToController: {
                    'sentenceId': '@',
                    'correctness': '='
                },
                scope: {},
                controller: ReviewsController,
                controllerAs: 'vm',
                restrict: 'E',
                templateUrl: 'edit-review-template'
            };
        });

    ReviewsController.$inject = ['$http'];
    function ReviewsController($http) {

        const rootUrl = get_tatoeba_root_url();
        var vm = this;
        vm.iconsInProgress = {};
        vm.setReview = setReview;
        vm.resetReview = resetReview;

        ///////////////////////////////////////////////////////////////////////////

        function setReview(value) {
            var reviewType = getReviewType(value);
            vm.iconsInProgress[reviewType] = true;
            $http.get(rootUrl + '/reviews/add_sentence/' + vm.sentenceId + '/' + value)
                 .then(function(response) {
                    vm.correctness = parseInt(response.data.result.correctness);
                    vm.iconsInProgress[reviewType] = false;
                 });
        }

        function resetReview() {
            var reviewType = getReviewType(vm.correctness);
            vm.iconsInProgress[reviewType] = true;
            $http.get(rootUrl + '/reviews/delete_sentence/' + vm.sentenceId)
                 .then(function(response) {
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
