(function(){
    'use strict';

    new AutocompleteBox("categories_tree/autocomplete", function(suggestion) {
        return suggestion.name;
    }, "parentName", "autocompletionParent");

    new AutocompleteBox("categories_tree/autocomplete", function(suggestion) {
        return suggestion.name;
    }, "categoryName", "autocompletionCategory");

    new AutocompleteBox("tags/autocomplete", function(suggestion) {
        return suggestion.name + ' (' + suggestion.nbrOfSentences + ')';
    }, "tagName", "autocompletionTag");

    angular.module('app').controller('CategoriesTreeController', [CategoriesTreeController]);

    function CategoriesTreeController() {
        var vm = this;
        vm.expandOrCollapse = expandOrCollapse;
        vm.displayedBranches = {};

        function expandOrCollapse(id) {
            vm.displayedBranches[id] = !vm.displayedBranches[id];
        }
    }
})();