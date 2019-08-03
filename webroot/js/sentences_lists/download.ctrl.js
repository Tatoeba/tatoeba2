(function(){
    angular.module('app').controller('SentencesListsDownloadCtrl', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {

        var rootUrl = get_tatoeba_root_url();

        $scope.export = {};
        $scope.preparingDownload = false;

        function startDownload() {
            var filename = encodeURIComponent($scope.export.pretty_filename);
            window.location = rootUrl + "/exports/download/" + $scope.export.id + "/" + filename;
            $scope.preparingDownload = false;
        }

        $scope.tryToDownloadList = function() {
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
                            $scope.tryToDownloadList();
                        }
                    },
                    function() {
                        $scope.preparingDownload = false;
                    }
                );
            }, 5000);
        }

        $scope.addListExport = function (listId) {
            $scope.export = {};
            $scope.preparingDownload = true;
            var fields = [];
            var options = {'type': 'list', 'list_id': listId};
            if ($scope.showid) {
                fields.push('id');
            }
            fields.push('text');
            if ($scope.trans_lang != 'none') {
                fields.push('trans_text');
                options['trans_lang'] = $scope.trans_lang;
            }
            options['fields'] = fields;
            $http.post(rootUrl + "/exports/add", options)
                 .then(
                    function(response) {
                        $scope.export = response.data.export;
                        $scope.tryToDownloadList();
                    },
                    function(errorResponse) {
                        $scope.preparingDownload = false;
                    }
                 );
        };
    }]);
})();
