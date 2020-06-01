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
    countBeforeRequest--;
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
        $.get(
            rootUrl + "/tags/autocomplete/" + encodeURIComponent(tag),
            function(data) {
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
    $("#TagTagName").val(suggestionStr);
    $("#TagTagName").focus();
    return false;
}
/**
 * transform the json result into html content
 */
function suggestShowResults(suggestions) {
    // we remove the old one
    removeSuggestList();
    if (suggestions.allTags.length == 0) {
        return;
    }

    suggestLength = suggestions.allTags.length;
    isSuggestListActive = true;

    var ul = document.createElement("ul");
    $("#autocompletionDiv").append(ul);
    suggestions.allTags.forEach(function(suggestion, index) {
        var text = document.createTextNode(suggestion.name + " (" + suggestion.nbrOfSentences + ")");

        var link = document.createElement("a");
        link.id = "suggestedItem" + index;
        link.dataset.tagName = suggestion.name;
        link.onclick = "suggestSelect(this.dataset.tagName)";
        link.style = "color:black";
        link.appendChild(text);

        var li = document.createElement("li");
        li.appendChild(link);

        ul.appendChild(li);
    });
}

/**
 *
 */
function changeActiveSuggestion(offset) {
    $("#suggestedItem"+currentSuggestPosition).removeClass("selected");
    currentSuggestPosition = (currentSuggestPosition + offset) % suggestLength;
    if (currentSuggestPosition < 0) {
        currentSuggestPosition = suggestLength - 1;
    }
    var selectedItem = $("#suggestedItem"+currentSuggestPosition);
    if (selectedItem.length > 0) {
        selectedItem.addClass("selected");
        suggestSelect(selectedItem[0].dataset.tagName);
    }
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
    $("#TagTagName").blur(function() {
        setTimeout(function() {
        removeSuggestList()},
        300
        );
    });

    $("#TagTagName").keyup(function(e){
        switch(e.keyCode) {
            case 38: //up
                if (isSuggestListActive) {
                    changeActiveSuggestion(-1);
                }
                break;
            case 40://down
                if (isSuggestListActive) {
                    changeActiveSuggestion(1);
                }
                break;
            case 27: //escape
            case 37: //left
            case 39: //right
                removeSuggestList();
                break;
            default: 
                var tag = $(this).val();
                countBeforeRequest++;
                setTimeout("sendToAutocomplete()",200);
                break;
        }
 
    });
});
