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
        vm.errors = function (parentId) {
            return Object.values(vm.validationErrors[parentId]).map(Object.values).flat();
        };

        vm.replies = {};
        vm.isSaving = {};
        vm.savedReplies = {};
        vm.hiddenReplies = {};
        vm.visibleForms = {};
        vm.validationErrors = {}

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
            vm.outboundLinksConfirmed = false;
        }

        function saveReply(id) {
            vm.isSaving[id] = true;

            var body = {
                'content': vm.replies[id],
                'replyTo': id,
            };
            if (vm.outboundLinksConfirmed) {
                body.outboundLinksConfirmed = '1';
            }

            var rootUrl = get_tatoeba_root_url();
            var req = {
                method: 'POST',
                url: rootUrl + '/wall/save_inside',
                data: body
            }

            $http(req).then(
                function success(response) {
                    vm.isSaving[id] = false;
                    vm.savedReplies[id] = response.data;
                    vm.validationErrors[id] = {};
                    vm.outboundLinksConfirmed = false;
                }, function error(response) {
                    vm.isSaving[id] = false;
                    vm.validationErrors[id] = response.data;
                }
            );
        }

        function expandOrCollapse(id) {
            vm.hiddenReplies[id] = !vm.hiddenReplies[id];
        }
    }
})();