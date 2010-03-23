/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.


*/

$(document).ready(function() {

// this javascript is really dependant to id of divs which 
// contains the id of the sentence for the moment, due to xhtml,
// the id is store that  id="_XXXX"  where XXXX is the sentence's id
// when HTML5 will be over we can use a special field to store data

    $(".translateLink").click(function(){
        var sentenceId = $(this).parent().attr("id").slice(1);
        var sentenceLang = $(this).parent().attr("lang");
        
        /*
         * Save translation.
         */
        function save(){
            var sentenceText = $("#_" + sentenceId + "_text").val();
            var selectLang = $("#translationLang_" + sentenceId).val();
            
            if($.trim(sentenceText) != ""){
                unbind(); // very important
                // This unbind() applies for the submit button and input field.
                
                $("#_" + sentenceId + "_translations").show();
                $("#_" + sentenceId + "_loading").show();
                $(".addTranslations").hide();
                
                $.post(
                    "http://" + self.location.hostname + ":" + self.location.port + "/sentences/save_translation",
                    {
                        "id": sentenceId,
                        "originLang": sentenceLang,
                        "selectLang": selectLang,
                        "value": sentenceText
                    },
                    function(data){
                        $("#session_expired").remove();
                        $("#_" + sentenceId + "_loading").hide();
                        $("#_" + sentenceId + "_translations").prepend(data);
                        $("#_" + sentenceId + "_text").val('');
                    },
                    "html"
                );

            }
        }
        
        /*
         * Function to unbind the handlers binded to the submit button, input field 
         * and cancel button. It is very important to unbind, otherwise a same 
         * translation will be save as many times as the user clicked on the 
         * "translate" icon.
         */
        function unbind(){
            $("#_" + sentenceId + "_submit").unbind('click');
            $("#translation_for_" + sentenceId + " li input").unbind('keypress');
            $("#_" + sentenceId + "_cancel").unbind('click');
        }
        
        // Displaying translation input and hiding translations
        $("#translation_for_" + sentenceId).show();
        $("#_" + sentenceId + "_text").focus();
        $("#_" + sentenceId + "_translations").hide();
        
        // Submitting translation by clicking on button
        $("#_" + sentenceId + "_submit").click(function(){
            save();
        });
        
        // Submitting translation by pressing enter
        // NOTE : this is annoying when entering Japanese or Chinese because
        // enter is used to validate the choice of kanjis
        // NOTE2: on Linux it's space which is used to validate
        $("#translation_for_" + sentenceId + " li input").keypress(function(e){
            if(e.keyCode == 13) {
                save();
            }
        });
        
        // Cancel
        $("#_" + sentenceId + "_cancel").click(function(){
            unbind(); // very important
            $("#_" + sentenceId + "_translations").show();
            $(".addTranslations").hide();
        });
    });

});
