/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

    var rootUrl = get_tatoeba_root_url();
    
    $('.editable-list-name').each(function() {
        $(this).editable(rootUrl + '/sentences_lists/save_name', {
            type      : 'text',
            indicator : "<div class='loader-small loader'></div>",
            tooltip   : $(this).attr('data-tooltip'),
            submit    : $(this).attr('data-submit'),
            cancel    : $(this).attr('data-cancel'),
            cssclass  : 'edit-list-name-form',
            event     : 'edit_list_name',
            onblur    : 'ignore',
            data      : function (value, config) {
                            return $('<div>').html(value).text(); // fix html entities
                        },
        });
    });

    $(".edit-list-name").bind("click", function() {
        $(this).parent().find('.editable-list-name').trigger("edit_list_name");
    });

});
