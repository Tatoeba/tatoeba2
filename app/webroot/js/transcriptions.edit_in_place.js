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

$(document).ready(function() {
    var rootUrl = get_tatoeba_root_url();

    // Show the transcribe buttons if there are some
    // hidden transcriptions
    $('.needsReview:hidden').each(function(index) {
        /* Move the toggling button in the menu */
        transcr = $(this);
        menu = transcr.closest(".sentences_set").find('.transcribe-buttons');
        button = transcr.find('.transcribe.option');
        button.click(function(event) {
            transcr.toggle();
        });
        button.toggle(true);
        menu.append(button);
    });

    function markupToStored(lang, text) {
        if (lang == 'ja-Hrkt') {
            text = text.replace(
                // Converts the kanji｛reading｝ notation into [kanji|reading]
                // \p{Hiragana}: \u3041-\u3096\u309d\u309e
                // \p{Katakana}: \u30a1-\u30fa\u30fd\u30fe
                // ー: \u30fc
                // ｛: \uff5b
                // ｝: \uff5d
                // 〈〉《》「」『』: \u3008-\u300f
                /([^\u3041-\u3096\u309d\u309e\u30a1-\u30fa\u30fd\u30fe\u30fc\u3008-\u300f]*)\uff5b([^\uff5d]*)\uff5d/g,
                '[$1|$2]'
            );
        }
        return text;
    }

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
            event     : 'edit_transcription',
            id        : 'divId',
            height    : false, // disable autoheight, we'll set it in onedit()
            onedit    : function(settings, self) {
                $(self).find("rt").hide();
                settings.height = $(self).height();
                $(self).find("rt").show();
            },
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

                // Format entered transcription
                var input = $(this).find("textarea");
                var entered = input.val();
                var storedFormat = markupToStored($(self).attr('lang'), entered);
                input.val(storedFormat);

                // Handle reset button
                if (self.resetClicked) {
                    settings.target = settings.target.replace(/\/save\//, '/reset/');
                }
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
        }).bind('edit_transcription', function(e) {
            $(this).find('textarea').keydown(function(event) {
                if (event.which == 13)
                    $(this).closest('form').submit();
            });
            // Add third reset button
            if ($(this).find('button').length == 2) {
                var editable = this;
                var reset = $('<button type="submit"/>');
                reset.html(div.attr('data-reset'));
                reset.click(function(event) {
                    // hack to distinguish submit event
                    // triggered from this button
                    editable.resetClicked = true;
                });
                $(editable).find('form').append(reset);
            }
        });

        $(".edit_transcription").bind("click", function() {
            $(this).closest('.transcriptionContainer').find('.transcription').trigger("edit_transcription");
        });
    });
});
