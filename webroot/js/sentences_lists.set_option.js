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
    angular.module('app').controller('optionsCtrl', ['$scope', '$http', function ($scope, $http) {

        $scope.visibilityProgress = false;
        $scope.visibilityEditable = false;

        $scope.visibilityChanged = function () {
            $scope.visibilityProgress = true;
            var listId = document.querySelector("input[name=visibility]").dataset.listId;
            var rootUrl = get_tatoeba_root_url();
            $http.post(
                rootUrl + "/sentences_lists/set_option/",
                { "listId": listId, "option": "visibility", "value": $scope.visibility }
            ).then(
                function (response) { $scope.visibilityProgress = false; }
            );
        };

        $scope.editableChanged = function (oldSetting) {
            $scope.editableProgress = true;
            var listId = document.querySelector("input[name=editable_by]").dataset.listId;
            var rootUrl = get_tatoeba_root_url();
            $http.post(
                rootUrl + "/sentences_lists/set_option/",
                { "listId": listId, "option": "editable_by", "value": $scope.editable }
            ).then(
                function (response) {
                    $scope.editableProgress = false;
                    if (response.data.editable_by === "no_one" || oldSetting === "no_one")
                        location.reload();
                }
            );
        };
    }]);

})();
