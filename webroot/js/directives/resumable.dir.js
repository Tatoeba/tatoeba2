(function() {
    'use strict';

    angular
        .module('app')
        .directive('resumable', ['storageService', resumable]);

    function resumable(storageService) {
        return {
            restrict: 'A',
            require: '?ngModel',
            link: link,
        }

        function link(scope, element, attrs, ngModel) {
            var key = attrs.id;

            element.on('change', function(event) {
                storageService.store(key, event.target.value);
            });

            let storedValue = storageService.get(key);
            if (storedValue) {
                element.val(storedValue);
                if (ngModel) {
                    ngModel.$setViewValue(storedValue);
                }
            }
        }
    }
})();
