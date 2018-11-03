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
    $(document).watch("addrule", function() {
        // Show the transcribe buttons if there are some
        // hidden transcriptions
        $('.needsReview.hidden').each(function(index) {
            var transcr = $(this);
            var menu = transcr.closest('.mainSentence')
                              .closest('.sentences_set')
                              .find('.transcribe-buttons');
            if (menu.length) {
                /* Transcription of the main sentence, not a translation */
                /* Move the show button in the menu */
                var button = transcr.find('.transcribe.option');
                if (button.length) {
                    button.click(function(event) {
                        button.remove();
                        transcr.toggle(true);
                    });
                    button.toggle(true);
                    menu.empty().append(button);
                }
            }
        });

        // For transcriptions marked with class 'blend',
        // replace the sentence content with the transcription
        $('.transcriptionContainer.blend').each(function() {
            var container = $(this);
            var transcr = container.find('.transcription');
            var sentence = container.closest('.sentence').find('.content .text');
            if (sentence.data('text') === undefined) {
                sentence.data('text', sentence.text());
            }
            sentence.html(transcr.html());
            container.toggle(false);

            var isMainSentence = container.closest('.translations').length == 0;
            if (isMainSentence) {
                // Allow to unblend back the transcription and the sentence
                // by clicking on the transcribe button
                var menu = container.closest('.sentences_set').find('.transcribe-buttons');
                var button = container.find('.transcribe.option');
                button.click(function(event) {
                    sentence.text(sentence.data('text'));
                    button.remove();
                    container.toggle(true);
                });
                button.toggle(true);
                menu.empty().append(button);
            }
            container.removeClass('blend');
        });

        function escapeUnicodeChar(c) {
            var code = c.charCodeAt(0);
            if (code >= 128) {
               c = '\\u' + ('0000' + c.charCodeAt(0).toString(16)).slice(-4);
            }
            return c;
        }

        function escapeUnicodeString(input) {
            var output = '';
            for (var i = 0, l = input.length; i < l; i++) {
                output += escapeUnicodeChar(input.charAt(i));
            }
            return output;
        }

        function uniRegExp(regex, flags) {
           return new RegExp(escapeUnicodeString(regex), flags);
        }

        function markupToStored(lang, text) {
            if (lang == 'ja-Hrkt') {
                // Converts the kanji｛reading｝ notation into [kanji|reading]
                var hiragana = 'ぁ-ゖーゝゞ'; // \p{Hiragana}
                var katakana = 'ァ-ヺーヽヾ'; // \p{Katakana}
                var punct = '　-〄〇-〠・'; // 。、「」etc.
                punct += '！＂＃＇（），．／：；？［＼］＾｀～｟｠'; // fullwitdh forms
                punct += ' '; // space
                var regex = '([^｝' + hiragana + katakana + punct + ']*)｛([^｝]*)｝';
                text = text.replace(uniRegExp(regex, 'g'), '[$1|$2]');
                text = text.replace(uniRegExp('｜', 'g'),  '|');
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
                ajaxoptions : {
                    success : function(result, status) {
                        div.editing = false;
                        div.parent().watch("replaceWith", result);
                    }
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
                        self.resetClicked = false;
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

                    // Display errors
                    $(self).prepend(xhr.responseText);

                    // Restore after reset button hackery
                    settings.target = settings.target.replace(/\/reset\//, '/save/');
                    return false; // don't reset the form
                },
                indicator : "<div class='sentence-loader loader'></div>",
                tooltip   : div.attr('title'),
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

        // review button
        $('.transcriptionContainer .option.review').click(function() {
            transcription = $(this).closest('.transcriptionContainer').find('.transcription');
            transcription.trigger('edit_transcription');
            transcription.find('form').submit();
        });
    });
});
