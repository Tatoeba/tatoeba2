(function(){
    angular.module('app')

    .controller('exportCtrl', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {

        var rootUrl = get_tatoeba_root_url();

        $scope.export = {};
        $scope.preparingDownload = false;

        function startDownload() {
            var filename = encodeURIComponent($scope.export.pretty_filename);
            window.location = rootUrl + "/exports/download/" + $scope.export.id + "/" + filename;
            $scope.preparingDownload = false;
        }

        $scope.tryToDownloadExport = function(waitForMs = 5000) {
            $timeout.cancel($scope.tryAgainPromise);
            $scope.tryAgainPromise = $timeout(function() {
                $http.get(rootUrl + "/exports/get/" + $scope.export.id)
                .then(
                    function(response) {
                        $scope.export = response.data.export;
                        if ($scope.export.status == 'online') {
                            startDownload();
                        } else if ($scope.export.status == 'failed') {
                            $scope.preparingDownload = false;
                        } else {
                            $scope.tryToDownloadExport();
                        }
                    },
                    function() {
                        $scope.preparingDownload = false;
                    }
                );
            }, waitForMs);
        }

        $scope.addExport = function (type, fields, extraParams = {}) {
            $scope.export = {};
            $scope.preparingDownload = true;

            var params = {
                'type': type,
                'fields': fields,
                'format': 'tsv'
            };
            Object.assign(params, extraParams);

            $http.post(rootUrl + "/exports/add", params)
                 .then(
                    function(response) {
                        $scope.export = response.data.export;
                        $scope.tryToDownloadExport(0);
                    },
                    function(errorResponse) {
                        $scope.preparingDownload = false;
                    }
                 );
        };
    }])

    .directive('customExportDownloadButton', function() {
        return {
            controller:   'exportCtrl',
            scope: {
                'text': '@',
                'type': '@',
                'fields': '<',
                'params': '<',
                'ngDisabled': '<'
            },
            restrict: 'E',
            templateUrl: 'custom-export-download-button-template'
        };
    });
})();
