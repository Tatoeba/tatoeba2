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
    var firstItemVisibility;

    $(
        '#sentences_group_' + sentenceId + ' .needsReview'
    ).each(function(index) {
        if (index == 0) {
            firstItemVisibility = $(this).is(":visible");
        }
        $(this).toggle(!firstItemVisibility);
    });
}

$(document).ready(function() {
    var rootUrl = get_tatoeba_root_url();

    // Show the transcribe button if there are some
    // hidden transcriptions
    $('.needsReview:hidden').each(function(index) {
       transcribeButton = $(this).parentsUntil($(".sentences_set"), ".sentences_set").find('.transcribe');
       transcribeButton.show();
    });

    $('.editable.transcription').each(function() {
        var div = $(this);
        var previousValue = {};

        sentenceId = div.parent().parent().attr('data-sentence-id');
        script = div.attr('data-script');
        saveUrl = rootUrl + '/transcriptions/save/' + sentenceId + '/' + script;

        $(this).editable(saveUrl, {
            type      : 'textarea',
            cancel    : div.attr('data-cancel'),
            submit    : div.attr('data-submit'),
            id        : 'divId',
            data : function(value, settings) {
                var contents = $('<span>').html(value);
                return contents.find('.markup').text() || contents.text();
            },
            callback : function(result, settings) {
                div.parent().replaceWith(result);
            },
            onsubmit  : function(settings, self) {
                // Save the submitted value to restore it on error
                $(self).find("textarea").each(function(idx) {
                    previousValue[idx] = $(this).val();
                });
                return true;
            },
            onerror   : function (settings, self, xhr) {
                // Go back to the previous editing state
                $(self).html(self.revert);
                self.editing = false;
                $(self).trigger(settings.event);

                // Restore the previous value
                $(self).find("textarea").each(function(idx) {
                    $(this).val(previousValue[idx]);
                });
                return false; // don't reset the form
            },
            indicator : '<img width="30" height="30" src="/img/loading.svg">',
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
