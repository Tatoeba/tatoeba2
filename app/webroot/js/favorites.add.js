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

    var sentenceDictionary = new Array();
    $('.sentenceContent').each(function(){
        var key = $(this).attr("data-sentence-id");
        var sentence = $(this).html();
        sentenceDictionary[key] = sentence;
    });

    $(document).on("click", ".favorite", function(){
        var favoriteId = $(this).attr("data-sentence-id");
        var favoriteOption = $(this);
        var action = 'remove_favorite';
        if (favoriteOption.hasClass("add")) {
            action = 'add_favorite';
        }

        var requestUrl = "/favorites/" + action + "/" + favoriteId;

        favoriteOption.html("<div class='loader-small loader'></div>");

        $.post(requestUrl, {}, function(data) {
            if(favoriteOption.parent().hasClass("favorite-page")){
                if(favoriteOption.hasClass("remove")){
                    favoriteOption.parent().parent().find(".content.column").html("Successfully Removed");
                    favoriteOption.parent().parent().find(".content.column").css("font-style", "italic");
                    favoriteOption.parent().parent().find(".nav.column").css("visibility", "hidden");
                    favoriteOption.parent().parent().find(".lang.column").css("visibility", "hidden");
                } else {
                    favoriteOption.parent().parent().find(".content.column").html(sentenceDictionary[favoriteId]);
                    favoriteOption.parent().parent().find(".content.column").css("font-style", "");
                    favoriteOption.parent().parent().find(".nav.column").css("visibility", "");
                    favoriteOption.parent().parent().find(".lang.column").css("visibility", "");
                }
            }
            favoriteOption.replaceWith(data);
        });
    });

});
