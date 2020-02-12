/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 (function(){
    angular.module('app').controller('optionsCtrl', function ($scope){

        $scope.visibilityChanged = function () {
            $(".is-public.loader-container").show();
            var listId = $("input[name=visibility]").attr('data-list-id');
            var rootUrl = get_tatoeba_root_url();
            $.post(
                rootUrl + "/sentences_lists/set_option/",
                { "listId": listId, "option": "visibility", "value": $scope.visibility },
                function () {$(".is-public.loader-container").hide();}
            );
        };

        $scope.editableChanged = function (oldSetting) {
            $(".is-editable.loader-container").show();
            var listId = $("input[name=editable_by]").attr('data-list-id');
            var newSetting = $scope.editable;
            var rootUrl = get_tatoeba_root_url();
            $.post(
                rootUrl + "/sentences_lists/set_option/",
                { "listId": listId, "option": "editable_by", "value": newSetting },
                function () {
                    $(".is-editable.loader-container").hide();
                    if (newSetting === "no_one" || oldSetting === "no_one")
                        location.reload();
                }
            );
        };
    });

})();
