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
    $("#SentenceText").keyup(function(e){
        if (e.keyCode == 13) {
            save();
        }
    });
    
    $("#submitNewSentence").click(function(){
        save();
    })
    
    function save(){

        var rootUrl = get_tatoeba_root_url();
        
        var sentenceText = $("#SentenceText").val();
        var selectedLang = $("#contributionLang").val();
        if ($.trim(sentenceText) != '') {
            $(".sentencesAddedloading").show();
            
            $.post(
                rootUrl + "/sentences/add_an_other_sentence",
                {
                    "value": sentenceText,
                    "selectedLang": selectedLang
                },
                function(data){
                    $("#session_expired").remove();
                    $(".sentencesAddedloading").hide();
                    $("#sentencesAdded").prepend(data);
                    $("#SentenceText").val("");
                },
                "html"
            );
        }
    }
});
