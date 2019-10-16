(function() {
    'use strict';

    angular
        .module('app')
        .controller('WallController', [
            '$http', '$location', '$anchorScroll', WallController
        ]);

    function WallController($http, $location, $anchorScroll) {
        var vm = this;

        vm.showForm = showForm;
        vm.hideForm = hideForm;
        vm.saveReply = saveReply;

        vm.replies = {};
        vm.isSaving = {};

        ///////////////////////////////////////////////////////////////////////////

        function showForm(id) {
            $location.hash('reply-form-' + id);
            $anchorScroll.yOffset = 50;
            $anchorScroll();

            $('#form-' + id).removeClass('ng-hide');
            $('#form-' + id + ' input').focus();
        }

        function hideForm(id) {
            $('#form-' + id).addClass('ng-hide');
        }

        function saveReply(id) {
            vm.isSaving[id] = true;

            var body = {
                'content': vm.replies[id],
                'replyTo': id,
            };

            var rootUrl = get_tatoeba_root_url();
            var req = {
                method: 'POST',
                url: rootUrl + '/wall/save_inside',
                data: body
            }

            $http(req).then(
                function() {
                    vm.isSaving[id] = false;
                    $('#form-' + id + ' .reply-saved').removeClass('ng-hide');
                    $('#form-' + id + ' .content').addClass('ng-hide');
                }
            );
        }

    }
})();