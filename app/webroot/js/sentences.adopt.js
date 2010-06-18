/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  Allan SIMON <allan.simon@supinfo.com>
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

    $(".adopt").click(function(){
        var adoptOption = $(this);
        var sentenceId = $(this).data("sentenceId");
        
        var rootUrl = get_tatoeba_root_url();
        
        // Displaying loading gif
        $("#sentences_group_" + sentenceId).html(
            "<img src='/img/loading.gif' alt='loading'>"
        );
        
        // The sentence can be adopted
        if (adoptOption.hasClass("add")){
            $("#sentences_group_" + sentenceId).load(
                rootUrl + "/sentences/adopt/"+  sentenceId
            );
        }
        
        // The sentence can be unadopted 
        else if (adoptOption.hasClass("remove")){
            $("#sentences_group_" + sentenceId).load(
                rootUrl + "/sentences/let_go/"+  sentenceId
            );
        }
    });
});

