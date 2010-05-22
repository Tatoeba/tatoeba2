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
        var host = self.location.hostname;
        var port = self.location.port;
        var lang = $(this).val();
        var interfaceLang = $("#randomLink").attr("lang");
        
        $("#randomLink").attr(
            "href",
            "http://"+host+":"+port+"/"+interfaceLang+"/sentences/show/"+lang
        );
        var currentId = $(this).data('currentSentenceId');
        // TODO make ajax request to get neighbors value
        $.post(
            "http://" + host + ":" + port + "/sentences/get_neighbors_for_ajax/"+currentId+"/"+ lang,
            {},
            function(data){
                neighbors = data.split(";");
                prevId = neighbors[0];
                nextId = neighbors[1];
                
                if (prevId == "") {
                    $("#prevSentence").attr("class","unactive");
                }
                // TODO set to active if not null
            
                if (nextId == "") {
                    $("#nextSentence").attr("class","unactive");
                }
                // same
            }
        );
    });
});
