/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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

/**
 *
 */
function get_language_interface_from_url() {
    pathArray = window.location.pathname.split('/');
    return pathArray[1];
}

function get_tatoeba_root_url() {
    var host = self.location.host;
    var interfaceLang = get_language_interface_from_url();

    return "//" + host + "/"+ interfaceLang;
}

/**
 * Returns string without the new lines and stuff.
 */
function normalized_sentence(sentenceText) {
    var reg = new RegExp("[\\t\\n\\r ]+", "gi");
    sentenceText = sentenceText.replace(reg, " ");
    return sentenceText;
}

/**
 * Change the language of the interface.
 */
function changeInterfaceLang(newLang) {
    // Saving the cookie
    var date = new Date();
    date.setMonth(date.getMonth()+1);
    document.cookie = 'CakeCookie[interfaceLanguage]=' + newLang
        + '; path=/'
        + '; expires=' + date.toGMTString();
    location.reload();
}


/**
 * Swaps languages of both drop down on clicking arrow in search box
 */


$(document).ready(function() {
    $( "#arrow" ).click(function() {
        var langFrom = $('#SentenceFrom').val();
        var langTo = $('#SentenceTo').val();
        $('#SentenceFrom').val(langTo);
        $('#SentenceTo').val(langFrom);
    });

    $('#clearSearch').click(function() {
        $('#SentenceQuery').val('').focus();
    });
});

/**
 * Traverses through paginated pages on Ctrl + Left/Right arrow
 * Only activated when focus is not on a textbox.
 */

function key_navigation() {
    $(document).bind("keydown", function(event) {
        // handle right page turn. 39 = char code for right arrow.
        if(event.ctrlKey && event.which == 39 && document.activeElement.type != "text" && document.activeElement.type != "textarea") {
            $("div.paging span.current").next().children("a")[0].click();
        }
        //handle left page turn. 37 = left arrow
        if(event.ctrlKey && event.which == 37 && document.activeElement.type != "text" && document.activeElement.type != "textarea") {
            $("div.paging span.current").prev().children("a")[0].click();
        }
    });
}

$(document).ready(function() {
    //shortcuts only show up if pagination is present
    if ($("div.paging").length > 0) {
        key_navigation();
    }
});

$(document).ready(function() {
    $(document).watch("addrule", function() {
        $('.sentenceContent .text').each(function() {
            var sentence = $(this);
            if (sentence.data('text') === undefined) {
                sentence.data('text', sentence.text());
            }
        });
    });
});

// Fix for Chrome: prevent copy-pasting furigana when selecting a sentence
// https://stackoverflow.com/questions/13438391
$(document).on('copy', function (e) {
    e.preventDefault();
    $('rt').css('visibility', 'hidden');
    e.originalEvent.clipboardData.setData('text', window.getSelection().toString());
    $('rt').css('visibility', 'visible');
});
