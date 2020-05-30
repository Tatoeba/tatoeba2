(function() {
    'use strict';

    angular
        .module('app')
        .factory('reviewsService', reviewsService)
        // .run(function(listsDataService) {
        //     listsDataService.init();
        // })

    function reviewsService($http) {
        const rootUrl = get_tatoeba_root_url();


        function setReview(value, sentenceId) {
            return $http.get(rootUrl + '/reviews/add_sentence/' + sentenceId + '/' + value);
        }

        function resetReview(sentenceId) {
            return $http.get(rootUrl + '/reviews/delete_sentence/' + sentenceId);
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


        return {
            setReview: setReview,
            resetReview: resetReview,
            getReviewType: getReviewType
        };
    }
})();
