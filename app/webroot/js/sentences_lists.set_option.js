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


$(document).ready(function() {

    $("input[name=visibility]").change(function(){
        var value = $(this).val();
        var listId = $(this).attr('data-list-id');

        $(".is-public.loader-container").show();

        setOption(listId, 'visibility', value, function(data){
            $("input[name=visibility][value="+value+"]").prop(
                'checked', data["visibility"] === value
            );
            $(".is-public.loader-container").hide();
        });
    });

    $("input[name=editable_by]").change(function(){
        var value = $(this).val();
        var listId = $(this).attr('data-list-id');

        $("#editableCheckbox").hide();

        $(".is-editable.loader-container").show();
        setOption(listId, 'editable_by', value, function(data){
            $("input[name=editable_by][value="+value+"]").prop(
                'checked', data["editable_by"] === value
            );
            $(".is-editable.loader-container").hide();
        });
    });

    function setOption(listId, optionName, optionValue, callback) {
        var rootUrl = get_tatoeba_root_url();
        $.post(
            rootUrl + "/sentences_lists/set_option/",
            { "listId": listId, "option": optionName, "value": optionValue },
            callback
        );
    }

});
