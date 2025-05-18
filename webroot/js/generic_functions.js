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

function get_csrf_token() {
    // Adapted from https://stackoverflow.com/a/15724300
    // Licensed under CC BY-SA 4.0
    var value = "; " + document.cookie;
    var parts = value.split("; csrfToken=");
    if (parts.length == 2) return parts.pop().split(";").shift();
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
 * Traverses through paginated pages on Ctrl + Left/Right arrow
 * Only activated when focus is not on a textbox.
 */
function key_navigation() {
    document.addEventListener("keydown", function(event) {
        // handle right page turn. 39 = char code for right arrow.
        if(event.ctrlKey && event.which == 39 && document.activeElement.type != "text" && document.activeElement.type != "textarea") {
            document.querySelector("ul.paging li.active").nextElementSibling.children[0].click();
        }
        //handle left page turn. 37 = left arrow
        if(event.ctrlKey && event.which == 37 && document.activeElement.type != "text" && document.activeElement.type != "textarea") {
            document.querySelector("ul.paging li.active").previousElementSibling.children[0].click();
        }
    });
}

document.addEventListener('DOMContentLoaded', function(){ 
    //shortcuts only show up if pagination is present
    if (document.getElementsByClassName("paging").length > 0) {
        key_navigation();
    }
}, false);

/**
 * Polyfills
 */
// https://tc39.github.io/ecma262/#sec-array.prototype.find
if (!Array.prototype.find) {
    Object.defineProperty(Array.prototype, 'find', {
      value: function(predicate) {
       // 1. Let O be ? ToObject(this value).
        if (this == null) {
          throw new TypeError('"this" is null or not defined');
        }
  
        var o = Object(this);
  
        // 2. Let len be ? ToLength(? Get(O, "length")).
        var len = o.length >>> 0;
  
        // 3. If IsCallable(predicate) is false, throw a TypeError exception.
        if (typeof predicate !== 'function') {
          throw new TypeError('predicate must be a function');
        }
  
        // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
        var thisArg = arguments[1];
  
        // 5. Let k be 0.
        var k = 0;
  
        // 6. Repeat, while k < len
        while (k < len) {
          // a. Let Pk be ! ToString(k).
          // b. Let kValue be ? Get(O, Pk).
          // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
          // d. If testResult is true, return kValue.
          var kValue = o[k];
          if (predicate.call(thisArg, kValue, k, o)) {
            return kValue;
          }
          // e. Increase k by 1.
          k++;
        }
  
        // 7. Return undefined.
        return undefined;
      },
      configurable: true,
      writable: true
    });
  }

// Fix for Chrome: prevent copy-pasting furigana when selecting a sentence
// https://stackoverflow.com/questions/13438391
document.addEventListener('copy', function (e) {
    // "currently getSelection() doesn't work on the content of <textarea> and
    // <input> elements in Firefox, Edge (not Chromium) and Internet Explorer"
    // according to https://developer.mozilla.org/en-US/docs/Web/API/Window/getSelection
    // Since those elements don't contain ruby tags, we can just ignore them.
    var tagName = e.target.tagName;
    if(tagName == 'INPUT' || tagName == 'TEXTAREA') return;

    var sel = window.getSelection();
    var clipboardData = e.originalEvent.clipboardData; // not available in IE
    var setData = clipboardData && clipboardData.setData; // not available in iOS Safari
    if (sel.rangeCount > 0 && setData) {
        document.getElementsByTagName('rt').css('visibility', 'hidden');
        clipboardData.setData('text', sel.toString());
        document.getElementsByTagName('rt').css('visibility', 'visible');
        e.preventDefault();
    }
});
