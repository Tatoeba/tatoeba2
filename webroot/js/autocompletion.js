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

class AutocompleteBox {
    constructor(url, format, inputId, divId) {
        this.url = get_tatoeba_root_url() + "/" + url + "/";
        this.format = format;

        this.currentSuggestPosition = -1;
        this.countBeforeRequest = 0;
        this.suggestLength = 0;
        this.previousText = '';
        this.isSuggestListActive = false;

        this.inputTag = "#" + inputId;
        this.divTag = "#" + divId;

        $(this.inputTag).attr("autocomplete", "off");
        $(this.divTag).addClass("autocompletionDiv");

        var that = this;
        $(this.inputTag).blur(function() {
            setTimeout(function() {
                that.removeSuggestList()
            }, 300);
        });

        $(this.inputTag).keyup(function(e){
            switch(e.keyCode) {
                case 38: //up
                    if (that.isSuggestListActive) {
                        that.changeActiveSuggestion(-1);
                    }
                    break;
                case 40://down
                    if (that.isSuggestListActive) {
                        that.changeActiveSuggestion(1);
                    }
                    break;
                case 27: //escape
                case 37: //left
                case 39: //right
                    that.removeSuggestList();
                    break;
                default:
                    this.countBeforeRequest++;
                    setTimeout(function(){
                        that.sendToAutocomplete()
                    }, 200);
                    break;
            }
        });
    }

    removeSuggestList() {
        this.isSuggestListActive = false;
        this.currentSuggestPosition = -1;
        $(this.divTag).empty();
    }

    changeActiveSuggestion(offset) {
        $("#suggestedItem" + this.currentSuggestPosition).removeClass("selected");
        this.currentSuggestPosition = (this.currentSuggestPosition + offset) % this.suggestLength;
        if (this.currentSuggestPosition < 0) {
            this.currentSuggestPosition = this.suggestLength - 1;
        }
        var selectedItem = $("#suggestedItem" + this.currentSuggestPosition);
        if (selectedItem.length > 0){
            selectedItem.addClass("selected");
            this.suggestSelect(selectedItem[0].dataset.tagName);
        }
    }

    suggestSelect(suggestionStr) {
        $(this.inputTag).val(suggestionStr);
        $(this.inputTag).focus();
        return false;
    }

    sendToAutocomplete() {
        this.countBeforeRequest--;
        if (this.countBeforeRequest > 0) {
            return;   
        }
        this.countBeforeRequest = 0;
    
        var tag = $(this.inputTag).val();
    
        if (tag == '') {
            $(this.divTag).empty();
            this.previousText = tag; 
            return;
        }

        var that = this;
        if (tag != this.previousText) {
            $.get(
                this.url + encodeURIComponent(tag),
                function(data) {
                    that.suggestShowResults(data);
                }
            );
            this.previousText = tag; 
        }
    }
    
    suggestShowResults(suggestions) {
        this.removeSuggestList();
        if (suggestions.results.length == 0) {
            return;
        }
    
        this.suggestLength = suggestions.results.length;
        this.isSuggestListActive = true;
        var that = this;
    
        var ul = document.createElement("ul");
        $(this.divTag).append(ul);
        suggestions.results.forEach(function(suggestion, index) {
            var text = document.createTextNode(that.format(suggestion));
        
            var link = document.createElement("a");
            link.id = "suggestedItem" + index;
            link.dataset.tagName = suggestion.name;
            link.addEventListener("click", function(){
                that.suggestSelect(suggestion.name)
            });
            link.style = "color:black";
            link.appendChild(text);

            var li = document.createElement("li");
            li.appendChild(link);
            
            ul.appendChild(li);
        });
    }
}