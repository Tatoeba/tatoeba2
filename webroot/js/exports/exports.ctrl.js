(function(){
    angular.module('app').controller('exportsCtrl', ['$scope', '$http', function ($scope, $http) {

        var rootUrl = get_tatoeba_root_url();

        $scope.init = function (exports) {
            $scope.exports = exports;
        }

        $scope.addListExport = function () {
            $http.post(rootUrl + "/exports/add", {
                     'type': 'list',
                     'list_id': $scope.selectedList,
                     'name': 'List ' + $scope.selectedList,
                     'description': 'Sentences from list ' + $scope.selectedList
                 })
                 .then(
                    function(response) { $scope.exports.push(response.data.export); }
                 );
        };
    }]);
})();
