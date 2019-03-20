(function(){
    angular.module('app').controller('switchLicenseCtrl', ['$scope', '$http', function ($scope, $http) {

        $scope.refreshList = function () {
            var rootUrl = get_tatoeba_root_url();
            $http.post(rootUrl + "/licensing/refresh_license_switch_list")
                 .then(
                    function() { $scope.isRefreshing = true; }
                 );
        };
    }]);
})();
