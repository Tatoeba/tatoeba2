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

    $('.editableSentence').each(function() {
        $(this).editable(rootUrl + '/sentences/edit_sentence', {
            type      : 'textarea',
            submit    : $(this).attr('data-submit'),
            cancel    : $(this).attr('data-cancel'),
            event     : 'edit_sentence',
            data : function(value, settings) {
                return $('<div>').html(value).text() // added to correct problem with html entities
            },
            indicator : '<img src="/img/loading.gif">',
            cssclass  : 'editInPlaceForm',
            onblur    : 'ignore'
        }).bind('edit_sentence', function(e) {
            $(this).find('textarea').keydown(function(event) {
                if (event.which == 13)
                    $(this).closest('form').submit();
            });
        });
    });

    $(".edit").bind("click", function() {
        $(this).parent().parent().find('.mainSentence .editableSentence').trigger("edit_sentence");
    });

    $(".editableTranslation .editableSentence").bind("click", function() {
        $(this).trigger("edit_sentence");
    });
});
