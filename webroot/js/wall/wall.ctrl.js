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
        vm.expandOrCollapse = expandOrCollapse;

        vm.replies = {};
        vm.isSaving = {};
        vm.savedReplies = {};
        vm.hiddenReplies = {};
        vm.visibleForms = {};

        ///////////////////////////////////////////////////////////////////////////

        function showForm(id) {
            $location.hash('reply-form-' + id);
            $anchorScroll.yOffset = 50;
            $anchorScroll();

            vm.visibleForms[id] = true;
            angular.element(document.querySelector('#reply-input-' + id)).focus();
        }

        function hideForm(id) {
            vm.visibleForms[id] = false;
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
                function(response) {
                    vm.isSaving[id] = false;
                    vm.savedReplies[id] = response.data;
                }
            );
        }

        function expandOrCollapse(id) {
            vm.hiddenReplies[id] = !vm.hiddenReplies[id];
        }
    }
})();