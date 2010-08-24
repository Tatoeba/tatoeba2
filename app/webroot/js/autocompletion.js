/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010   Allan SIMON <allan.simon@supinfo.com>
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

// we need to declare this variable and function out of the "documentready"
// block to make them somewhat global

// The mechanism is more or less the following
// Each time you input new character it increase a counter and start a timer
// at the end of the timer, it decrease the counter by one and check if the
// counter is zero and will call the ajax action only if it's zero
// This way we're sure the ajax action will occur at the expiration of the
// last counter
// This both avoid two hackish solution
//    1 send after each input (send too many request)
//    2 send each X ms (send also too many request)

var currentSuggestPosition = -1;
var countBeforeRequest = 0;
var suggestLength = 0;
var previousText = '';
var isSuggestListActive = false;

function sendToAutocomplete() {
    countBeforeRequest--
    if (countBeforeRequest > 0) {
        return;   
    } 

    var tag = $("#TagTagName").val();

    if (tag == '') {
        $("#autocompletionDiv").empty();
        previousText = tag; 
        return;
    }
    var rootUrl = get_tatoeba_root_url();
    if ( tag != previousText) {
        $.post(
            rootUrl + "/autocompletions/request/" + tag
            , {}
            , function(data) {
                suggestShowResults(data);
            }
        );
        previousText = tag; 
    }
};

/**
 * replace the input text by the clicked one
 */
function suggestSelect(suggestionStr) {
    $("#TagTagName").attr("value", suggestionStr);
    $("#TagTagName").focus();
    return false;
}
/**
 * transform the xml result into html content
 */
function suggestShowResults(xmlDocResults) {
    // we remove the old one
    removeSuggestList();
    suggestions = xmlDocResults.getElementsByTagName('item');

    if (suggestions.length == 0) {
        return;
    }
    suggestLength = suggestions.length;

    var ul = document.createElement("ul");
    $("#autocompletionDiv").append(ul);
    isSuggestListActive = true;
    for (var i in suggestions) {
        // for some weird reason the last element in suggestion is the number
        // of element in the array Oo
        // and even a function oO wow we're leaving in a strange world .... 
        if (!isNaN(parseInt(suggestions[i])) || $.isFunction(suggestions[i])) {
            continue;
        }
        suggestion = suggestions[i].firstChild.data;
        var li = document.createElement("li");
        li.innerHTML = "<a id='suggestItem" + i + "'onclick='suggestSelect(this.innerHTML)' style='color:black;'>"+
            suggestion +
        "</a>";
        ul.appendChild(li);
    }
    changeActiveSuggestion(1);
}

/**
 *
 */
function changeActiveSuggestion(offset) {
    $("#suggestItem"+currentSuggestPosition % suggestLength).removeClass("selected");
    currentSuggestPosition += offset;
    if (currentSuggestPosition < 0) {
        currentSuggestPosition = suggestLength - 1;
    }
    $("#suggestItem"+currentSuggestPosition % suggestLength).addClass("selected");
} 

/**
 *
 */
function removeSuggestList() {
    isSuggestListActive = false;
    currentSuggestPosition = -1;
    $("#autocompletionDiv").empty();
}
   

$(document).ready(function()
{


    // it desactivates browsers autocompletion
    // TODO: it's not something in the standard, so if you 
    // know a standard way to do this ...
    $("#TagTagName").attr("autocomplete","off");

    $("#TagTagName").blur(function() {
        setTimeout(function() {
        removeSuggestList()},
        300
        );
    });
 
    $("#TagTagName").keyup(function(e){
        switch(e.keyCode) {
            case 38: //up
                changeActiveSuggestion(-1);
                break;
            case 40://down
                changeActiveSuggestion(1);
                break;
            case 13: //enter
                break;
            case 27: //escape
                removeSuggestList();
                break;
            default: 
                var tag = $(this).val();
                countBeforeRequest++;
                setTimeout("sendToAutocomplete()",200);
                break;
        }
 
    });

    $("#TagAddTagForm").submit(function(){
        if (isSuggestListActive) {
            var text = $("#suggestItem"+currentSuggestPosition).html()
            $("#TagTagName").val(text);
            removeSuggestList(); 
            return false;
        }
    });
       
});
