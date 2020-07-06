(function(){
    'use strict';

    new AutocompleteBox("parenttag", "autocompletionParent");
    new AutocompleteBox("childtag", "autocompletionChild");

    angular
        .module('app')
        .controller('TagsLinksController', [
            '$http', '$location', '$anchorScroll', TagsLinksController
        ]);
    
    function TagsLinksController($http, $location, $anchorScroll) {
        var vm = this;
        vm.expandOrCollapse = expandOrCollapse;
        vm.hiddenReplies = {};

        function expandOrCollapse(id) {
            console.log("e");
            vm.hiddenReplies[id] = !vm.hiddenReplies[id];
        }
    }
})();