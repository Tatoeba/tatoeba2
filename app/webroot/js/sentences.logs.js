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
    $(".annexeLogEntry").each(function(){
        var historyRecord = $(this);

        historyRecord.find('a').each(function(){
            var historyRecordSentenceLink = $(this).attr('href');
            var sentence = $(".sentenceContent > a[href='" + historyRecordSentenceLink + "']").parent();
            if (sentence.length) { // we have this sentence displayed on the page
                var updateHighlight = function() {
                    $(".sentenceContent.highlighted").removeClass("highlighted");
                    $(".annexeLogEntry.historyHighlighted").removeClass("historyHighlighted");
                    sentence.addClass("highlighted");
                    historyRecord.addClass("historyHighlighted");
                };

                historyRecord.hover(updateHighlight, null);
                // Touchscreens cannot (or simulate) hovering, so let them use simple click instead
                historyRecord.click(function() { updateHighlight() });
            }
        });
    });
});
