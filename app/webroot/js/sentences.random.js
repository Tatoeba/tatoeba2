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


$(document).ready(function(){
    $("#randomLangChoiceInBrowse").change(function(){
        var currentId = $(this).data('currentSentenceId');
        var lang = $(this).val();
        var rootUrl = get_tatoeba_root_url();
        var interfaceLang = $("#randomLink").attr("lang");
        var baseURL = rootUrl + "/sentences/show/";
        
        
        // Showing loading animation
        $("#loadingAnimationForNavigation").show();
        
        // Update random link
        $("#randomLink a").attr("href",baseURL+lang);
        
        
        $.post(
            rootUrl + "/sentences/get_neighbors_for_ajax/"+currentId+"/"+ lang,
            {},
            function(data){
                neighbors = data.split(";");
                prevId = neighbors[0];
                nextId = neighbors[1];
                
                // Update "previous" link
                if (prevId == "") {
                    $("#prevSentence").attr("class", "inactive");
                    $("#prevSentence a").attr("href", "");
                } else {
                    $("#prevSentence").attr("class", "active");
                    $("#prevSentence a").attr("href",baseURL+prevId);
                }
                
                // Update "next" link
                if (nextId == "") {
                    $("#nextSentence").attr("class", "inactive");
                    $("#nextSentence a").attr("href", "");
                } else {
                    $("#nextSentence").attr("class", "active");
                    $("#nextSentence a").attr("href", baseURL+nextId);
                }
                
                $("#loadingAnimationForNavigation").hide();
            }
        );
    });
});
