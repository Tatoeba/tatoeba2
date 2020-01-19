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
        var sentenceDictionary = new Array();
        $('.sentenceContent').each(function(){
            var key = $(this).attr("data-sentence-id");
            var sentence = $(this).html();
            sentenceDictionary[key] = sentence;
        });

        var successString = $("#favorites-list").attr("data-success");
        var clickHandler = function() {
            var favoriteId = $(this).attr("data-sentence-id");
            var favoriteOption = $(this);
            var action = 'remove_favorite';
            if (favoriteOption.hasClass("add")) {
                action = 'add_favorite';
            }

            var requestUrl = get_tatoeba_root_url() + "/favorites/" + action + "/" + favoriteId;
            if(favoriteOption.parent().hasClass("favorite-page")){
                requestUrl += "/true";
            }
            favoriteOption.html("<div class='loader-small loader'></div>");
            $.get(requestUrl, function(data) {
                if(favoriteOption.parent().hasClass("favorite-page")){
                    if(favoriteOption.hasClass("remove")){
                        favoriteOption.parent().parent().find(".content.column").html(successString);
                        favoriteOption.parent().parent().find(".content.column").addClass("add");
                        favoriteOption.parent().parent().find(".content.column").removeClass("remove");
                        favoriteOption.parent().parent().find(".nav.column").css("display", "none");
                        favoriteOption.parent().parent().find(".lang.column").css("display", "none");
                    } else {
                        favoriteOption.parent().parent().find(".content.column").removeClass("add");
                        favoriteOption.parent().parent().find(".content.column").addClass("remove");
                        favoriteOption.parent().parent().find(".content.column").html(sentenceDictionary[favoriteId]);
                        favoriteOption.parent().parent().find(".nav.column").css("display", "");
                        favoriteOption.parent().parent().find(".lang.column").css("display", "");
                    }
                }
                favoriteOption.watch("replaceWith", data);
            });
        };
        $('.favorite').off();
        $('.favorite').click(clickHandler);
    });
});
