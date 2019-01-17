/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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
        $(".add-to-corpus").off('click').click(function(){

            var sentenceId = $(this).attr("data-sentence-id");
            var correctness = $(this).attr("data-sentence-correctness");
            var addToCorpusOption = $(this);

            var requestUrl = "/collections";
            if ($(this).hasClass("selected")){
                requestUrl += "/delete_sentence/" + sentenceId + "/" + correctness;
            } else {
                requestUrl += "/add_sentence/" + sentenceId + "/" + correctness;
            }

            addToCorpusOptionParent = addToCorpusOption.parent();
            addToCorpusOption.html("<div class='loader-small loader'></div>");


            $.get(requestUrl, function(data){
                addToCorpusOptionParent.watch("replaceWith", data);
            });
        });
    });
});
