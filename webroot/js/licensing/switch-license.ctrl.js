(function(){
    angular.module('app').controller('switchLicenseCtrl', ['$scope', '$http', '$interval', function ($scope, $http, $interval) {

        $scope.init = function (isRefreshing) {
            $scope.isRefreshing = isRefreshing;
            refreshingStateChanged(isRefreshing);
        };

        var rootUrl = get_tatoeba_root_url();
        $scope.refreshList = function () {
            $http.post(rootUrl + "/licensing/refresh_license_switch_list")
                 .then(
                    function() { $scope.isRefreshing = true; }
                 );
        };

        var stop;
        var stopCheckingForList = function() {
            if (angular.isDefined(stop)) {
                $interval.cancel(stop);
                stop = undefined;
            }
        };
        var startCheckingForList = function () {
            stop = $interval(function() {
                $http.get(rootUrl + "/licensing/get_license_switch_list")
                .then(
                    function(response) {
                        angular.element('switchList').replaceWith(response);
                        $scope.isRefreshing = false;
                    },
                    function() { /* a callback to prevent console warning */ }
                );
            }, 2000);
        };

        var refreshingStateChanged = function(newValue) {
            if (newValue == true) {
                startCheckingForList();
            } else {
                stopCheckingForList();
            }
        };
        $scope.$watch('isRefreshing', function(newValue, oldValue) {
            if (oldValue == !newValue) {
               refreshingStateChanged(newValue);
            }
        });

        // Make sure that the $interval callback is destroyed
        $scope.$on('$destroy', stopCheckingForList);
    }]);
})();
