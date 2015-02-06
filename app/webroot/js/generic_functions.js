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
    
    return "http://" + host + "/"+ interfaceLang;
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

$(document).ready(function() {
    $( "#arrow" ).click(function() {
        var langFrom = $('#SentenceFrom').val();
        var langTo = $('#SentenceTo').val();
	$('#SentenceFrom').val(langTo);
	$('#SentenceTo').val(langFrom);
    });
});
