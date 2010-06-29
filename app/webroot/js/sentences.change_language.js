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
    
    $(".languageFlag").unbind('click');
    // NOTE: It's important to unbind because when adding two translations in a row,
    // it will rebind again the same function. So the second time the user clicks
    // on a flag, the function is triggered twice and therefore it looks like 
    // nothing happens. But what happens is that the <select> is displayed then
    // hidden right away.
    
    $(".languageFlag").click(function(){
        var flagImage = $(this);
        
        // The data is set in sentence_buttons.php, displayLanguageFlag()
        var sentenceId = $(this).data('sentenceId');
        var prevLang = $(this).data('currentLang');
        
        $("#selectLang_" + sentenceId).toggle();
        
        $("#selectLang_" + sentenceId).change(function(){
        
            var newLang = $(this).val();
            var rootUrl = get_tatoeba_root_url();
            
            flagImage.attr('src', '/img/loading-small.gif');
            $("#selectLang_" + sentenceId).hide();
            
            $.post(
                rootUrl + "/sentences/change_language/",
                { "id": sentenceId, "newLang": newLang, "prevLang": prevLang },
                function(data){
                    $("#_" + sentenceId + "_in_process").hide();
                    flagImage.attr('src', '/img/flags/' + data + '.png');
                }
            );
        });
        
    });
    
});
