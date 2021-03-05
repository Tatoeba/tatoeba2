(function(){
    angular.module('app').controller('exportsCtrl', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {

        var rootUrl = get_tatoeba_root_url();

        function updateExports(newExports) {
            for (var i = 0; i < newExports.length; i++) {
                var newExport = newExports[i];
                Object.getOwnPropertyNames(newExport).forEach(
                    function (propName) {
                        $scope.exports[i][propName] = newExport[propName];
                    }
                );
            };
            $scope.exports.length = newExports.length;
        }

        $scope.maybeRefreshExportList = function() {
            $timeout.cancel($scope.refreshPromise);
            var notCompleted = $scope.exports
                                   .filter(function(e) {
                                       return e.status != 'online';
                                   })
                                   .length;
            if (notCompleted) {
                $scope.refreshPromise = $timeout(function() {
                    $http.get(rootUrl + "/exports/list")
                    .then(
                        function(response) {
                            updateExports(response.data.exports);
                            $scope.maybeRefreshExportList();
                        },
                        function() {
                            $scope.maybeRefreshExportList();
                        }
                    );
                }, 5000);
            }
        }

        $scope.init = function (exports) {
            $scope.exports = exports;
            $scope.maybeRefreshExportList();
            $scope.MAX_LATEST_EXPORTS = 5;
        }

        $scope.addExport = function (type, fields, extraParams = {}) {
            var params = {
                'type': type,
                'fields': fields,
                'format': 'tsv'
            };
            Object.assign(params, extraParams);
            $http.post(rootUrl + "/exports/add", params)
                 .then(
                    function(response) {
                        $scope.exports.push(response.data.export);
                        $scope.maybeRefreshExportList();
                    }
                 );
        };
    }]);
})();
