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
    //Hide all forms
    $('.editform').hide();
    //If any 'edit' button is clicked:
    $('li.edit').click(function(e) {
        //Show only the corresponding edit form.
        $(this).parent().parent().find('.editform').show();
        //Set value of edit form equal to corresponding sentence.
        $(".editform input").val($(this).parent().parent().find('.editableSentence').text()).show();
        //hide the corresponding sentence.
        $(this).parent().parent().find('.editableSentence').hide();
    });

    $('.editableSentence').bind("keydown", function(event) {
        // If enter key is pressed, press closest 'OK'
        if(event.which == 13) {
            $(this).parent().parent().find('.ok_button').click();
        }
    });
});

function cancel_edit() {
    //Hide all forms
    $(".editform").hide();
    //Clean all form values (to prevent browser warnings)
    $(".editform input").val('');
    //Show all sentences
    $(".editableSentence").show();
}
