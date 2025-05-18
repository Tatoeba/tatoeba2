/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
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

// Protect $ symbol
(function($) {
    $(function() {
        var rules = [];

        // Run currently registered rules in given context
        function applyRules(context) {
            $.each(rules, function(i, rule) {
                // Pass context as 'this' to the function
                rule.call(context);
            });
        };
        
        // Register jQuery plug-in
        $.fn.watch = function (action, obj) {
            switch (action) {
                case 'addrule':
                    // Remember the rule
                    rules.push(obj);
                    // Initialize over current scope
                    obj.call(this);
                    break;

                case 'append':
                case 'prepend':
                case 'replaceWith':
                case 'html':
                    this[action]($(obj));
                    applyRules(this);
                    break;

                default:
                  throw 'Invalid action: "' + action + '"';
            }
        };
    }); 
}(jQuery));


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