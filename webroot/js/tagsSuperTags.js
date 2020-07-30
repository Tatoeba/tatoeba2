(function(){
    'use strict';

    new AutocompleteBox("super_tags/autocomplete", function(suggestion) {
        return suggestion.name;
    }, "parent", "autocompletionParent");

    $("#child_type").on('change', function(){
        if (parseInt(this.value) == 0) {
            new AutocompleteBox("tags/autocomplete", function(suggestion) {
                return suggestion.name + " (" + suggestion.nbrOfSentences + ")";
            }, "child", "autocompletionChild");
        } else {
            new AutocompleteBox("super_tags/autocomplete", function(suggestion) {
                return suggestion.name;
            }, "child", "autocompletionChild");
        }
    });

    

    angular
        .module('app')
        .controller('TagsSuperTagsController', [
            '$http', '$location', '$anchorScroll', TagsSuperTagsController
        ]);
    
    function TagsSuperTagsController($http, $location, $anchorScroll) {
        var vm = this;
        vm.expandOrCollapse = expandOrCollapse;
        vm.hiddenReplies = {};

        function expandOrCollapse(id) {
            console.log("e");
            vm.hiddenReplies[id] = !vm.hiddenReplies[id];
        }
    }
})();