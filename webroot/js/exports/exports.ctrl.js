(function(){
    angular.module('app').controller('exportsCtrl', ['$scope', function ($scope) {
        $scope.init = function (exports) {
            $scope.exports = exports;
        };
    }]);
})();
