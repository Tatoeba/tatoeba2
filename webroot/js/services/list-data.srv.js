(function() {
    'use strict';

    angular
        .module('app')
        .factory('listsDataService', listsDataService)
        .run(function(listsDataService) {
            listsDataService.init();
        })

    function listsDataService($http) {
        const rootUrl = get_tatoeba_root_url();
        
        var lists;

        return {
            init: init,
            getLists: getLists
        };

        /////////////////////////////////////////////////////////////////////////

        function init() {
            if (!lists) {
                $http.get(rootUrl + '/sentences_lists/choices').then(function(result) {
                    lists = result.data.lists;
                });
            }
        }

        function getLists() {
            return lists;
        }
    }
})();