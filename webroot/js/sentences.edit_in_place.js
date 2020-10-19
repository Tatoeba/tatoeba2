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
    $(document).watch("addrule", function() {
        $('.editableSentence').each(function() {
            var div = $(this);

            var sentenceId = div.parent().attr('data-sentence-id');
            div.editable(rootUrl + '/sentences/edit_sentence', {
                type      : 'textarea',
                submit    : div.attr('data-submit'),
                onsubmit  : function () {
                    if($(this).find('textarea').val().trim().length == 0){
                        return false;
                    } else {
                        return true;
                    }
                },
                cancel    : div.attr('data-cancel'),
                event     : 'edit_sentence',
                data : function(value, settings) {
                    return $(this).data('text');
                },
                name      : 'text',
                submitdata : {'id': sentenceId},
                callback : function(result, settings) {
                    var text = $('<div>').html(result).text(); // fix html entities
                    $(this).data('text', text);
                    // Update transcriptions if any
                    var transcr = div.closest('.sentence').find('.transcriptions');
                    if (transcr.length) {
                        transcr.html("<div class='sentence-loader loader'></div>");
                        $.get(
                            rootUrl + '/transcriptions/view/' + sentenceId,
                            null,
                            function(result, status) {
                                transcr.watch('html', result);
                            }
                        );
                    }
                },
                indicator : "<div class='sentence-loader loader'></div>",
                cssclass  : 'editInPlaceForm',
                onblur    : 'ignore'
            }).bind('edit_sentence', function(e) {
                $(this).find('textarea').keyup(function(event) {
                    var submitBtn = $(this).parent().find('button[type=submit]');
                    if($(this).val().trim().length == 0) {
                        submitBtn.prop('disabled', true);
                    }
                    else {
                        submitBtn.prop('disabled', false);
                    }
                    
                }); 
                $(this).find('textarea').keydown(function(event) {
                    if (event.which == 13){
                        event.preventDefault();
                        $(this).closest('form').submit();   
                    }
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
});
