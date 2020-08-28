(function() {
    'use strict';

    angular
        .module('app')
        .service('storageService', ['$window', '$cookies', storageService]);

    function storageService($window, $cookies) {
        var storage = null;
        /* Remove language prefix from URL path */
        var currentPage = $window.location.pathname.replace(/^\/\w+\//, '');

        init();

        return {
            store: store,
            get: get,
            remove: remove,
        }

        /**
         * Initialize the service
         */
        function init() {
            try {
                storage = $window.sessionStorage;
                const test = '__tatoeba_storage_test__';
                storage.setItem(test, test);
                storage.removeItem(test);
            } catch(e) {
                /* sessionStorage not available */
                storage = null;
            }

            /**
             * Remove stored value when form was succesfully submitted.
             * The controller should set a cookie with key "submitted" and
             * value "<form-id>@<path> where <form-id> is the "id" attribute
             * of the form and <path> the URL path without the language prefix
             * and without the initial "/".
             */
            let submitted = $cookies.get('submitted');
            if (submitted) {
                let parts = submitted.split('@');
                let key = parts[0];
                let pageKey = parts[1];
                remove(key, pageKey);
                $cookies.remove('submitted');
            }
        }

        /**
         * Store a value
         *
         * key:                Key
         * value:              Value
         * pageKey [optional]: Either the path for the page or the current page
         *                     when not given
         */
        function store(key, value, pageKey) {
            if (storage) {
                if (pageKey === undefined) { pageKey = currentPage; }
                let items = JSON.parse(storage.getItem(pageKey)) || Object.create(null);
                items[key] = value;
                storage.setItem(pageKey, JSON.stringify(items));
            }
        }

        /**
         * Retrieve a value
         *
         * key:                Key
         * pageKey [optional]: Either the path for the page or the current page
         *                     when not given
         */
        function get(key, pageKey) {
            if (storage) {
                if (pageKey === undefined) { pageKey = currentPage; }
                let items = JSON.parse(storage.getItem(pageKey));
                return items ? items[key] : '';
            } else {
                return '';
            }
        }

        /**
         * Remove a value
         *
         * key:                Key
         * pageKey [optional]: Either the path for the page or the current page
         *                     when not given
         */
        function remove(key, pageKey) {
            if (storage) {
                if (pageKey === undefined) { pageKey = currentPage; }
                let items = JSON.parse(storage.getItem(pageKey));
                delete items[key];
                if (Object.entries(items).length > 0) {
                    storage.setItem(pageKey, JSON.stringify(items));
                } else {
                    storage.removeItem(pageKey);
                }
            }
        }
    }
})();
