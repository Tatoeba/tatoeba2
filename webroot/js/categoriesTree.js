(function(){
    'use strict';

    new AutocompleteBox("categories_tree/autocomplete", function(suggestion) {
        return suggestion.name;
    }, "parentName", "autocompletionParent");

    new AutocompleteBox("categories_tree/autocomplete", function(suggestion) {
        return suggestion.name;
    }, "categoryName", "autocompletionCategory");

    new AutocompleteBox("tags/autocomplete", function(suggestion) {
        return suggestion.name + '(' + suggestion.nbrOfSentences + ')';
    }, "tagName", "autocompletionTag");

    angular
        .module('app')
        .controller('CategoriesTreeController', [
            '$http', '$location', '$anchorScroll', CategoriesTreeController
        ]);
    
    function CategoriesTreeController($http, $location, $anchorScroll) {
        var vm = this;
        vm.expandOrCollapse = expandOrCollapse;
        vm.hiddenReplies = {};

        function expandOrCollapse(id) {
            console.log("e");
            vm.hiddenReplies[id] = !vm.hiddenReplies[id];
        }
    }
})();