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

$(document).ready(function(){
    $(document).watch("addrule", function() {
        $(".link-insert").each(function(){
            var historyRecord = $(this);
            var sentenceId = historyRecord.attr('data-translation-id');
            var sentence = $(".directTranslation[data-sentence-id='" + sentenceId + "']");
            if (sentence.length) { // we have this sentence displayed on the page
                var updateHighlight = function() {
                    $(".directTranslation.highlighted").removeClass("highlighted");
                    $(".link-insert.historyHighlighted").removeClass("historyHighlighted");
                    sentence.addClass("highlighted");
                    historyRecord.addClass("historyHighlighted");
                    $.scrollTo(sentence, 500, {offset: -100});
                };
                var addHover = function() {
                    historyRecord.addClass("hovered");
                };
                var removeHover = function() {
                    $(".link-insert.hovered").removeClass("hovered");
                }
                historyRecord.hover(addHover, removeHover);
                historyRecord.click(function() { updateHighlight() });
            }
        });
    });
});
