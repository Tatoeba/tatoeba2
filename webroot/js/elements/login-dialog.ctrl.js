(function() {
    'use strict';

    angular
        .module('app')
        .controller('LoginDialogController', ['$mdDialog', function($mdDialog) {
            var vm = this;

            vm.showDialog = showDialog;
            
            ///////////////////////////////////////////////////////////////////////////

            function showDialog(url) {
                $mdDialog.show({
                    controller: DialogController,
                    templateUrl: get_tatoeba_root_url() + '/users/login_dialog_template?redirect=' + encodeURIComponent(url),
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    fullscreen: true
                });
            }

            function DialogController($scope, $mdDialog) {
                $scope.close = function() {
                    $mdDialog.cancel();
                };
            }
        }]);
})();
