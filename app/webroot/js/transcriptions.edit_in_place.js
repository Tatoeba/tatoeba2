/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014 Gilles Bedel
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

function displayTranscriptions(sentenceId) {
    $(
        '#sentences_group_' + sentenceId + ' .generatedTranscription'
    ).toggle();
}

$(document).ready(function() {
    var rootUrl = get_tatoeba_root_url();

    $('.editable.transcription').each(function() {
        var div = $(this);

        sentenceId = div.attr('data-sentence-id');
        script = div.attr('data-script');
        saveUrl = rootUrl + '/transcriptions/save/' + sentenceId + '/' + script;

        $(this).editable(saveUrl, {
            type      : 'textarea',
            cancel    : 'Cancel',
            submit    : 'OK',
            id        : 'divId',
            data : function(value, settings) {
                return $('<div>').html(value).text() // added to correct problem with html entities
            },
            callback : function(result, settings) {
                div.parent().replaceWith(result);
            },
            indicator : '<img src="/img/loading.gif">',
            tooltip   : div.attr('data-tooltip'),
            cssclass  : 'editInPlaceForm',
            onblur    : 'ignore'
        }).click(function(e) {
            $(this).find('textarea').keydown(function(event) {
                if (event.which == 13)
                    $(this).closest('form').submit();
            });
        });
    });
});
