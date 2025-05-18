/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
    $("#text").keydown(function(e){
        if (e.keyCode == 13) {
            save();
        }
    });
    
    $("#submitNewSentenceToList").click(function(){
        save();
    });
    
    function save(){
        $("#list_add_loader").show();
        var sentenceText = $("#text").val();
        var listId = $("#sentencesList").attr('data-list-id');
        var rootUrl = get_tatoeba_root_url();
        

        $.post(rootUrl + "/sentences_lists/add_new_sentence_to_list/"
        , { "listId": listId, "sentenceText" : sentenceText, "sentenceLang" : "auto" }
        , function(data){
            $("#text").val("");
            $("#session_expired").remove();
            $(".sentencesList").watch("prepend", data);
            $("#list_add_loader").hide();
        }
        , "html");        
    }
});
