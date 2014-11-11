/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014  Gilles Bedel
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

function linkToSentence(sentenceId) {
    var linkTo = function(){
        var rootUrl = get_tatoeba_root_url();
        var linkToSentenceId = $("#linkToSentence" + sentenceId).val();

        // if the field appears to contain a link to another sentence,
        // extract the sentence number from it
        var linkParse = linkToSentenceId.match(/\/(\d+)$/);
        if (linkParse) {
            linkToSentenceId = linkParse[1];
        }

        // ensure the form contains a number
        if (linkToSentenceId != parseInt(linkToSentenceId)) {
            return;
        }

        $("#_" + sentenceId + "_translations").hide();
        $("#_" + sentenceId + "_message").remove();
        $("#_" + sentenceId + "_loading").show();

        $(this).unbind('click', linkTo); // to prevent double submission
        $.post(
            rootUrl + "/links/add/" + sentenceId + "/" + linkToSentenceId,
            {
                'returnTranslations': true
            },
            function(data){
                $("#_" + sentenceId + "_loading").hide();
                $("#_" + sentenceId + "_translations").replaceWith(data).show();
                $("#linkTo" + sentenceId).hide();
                $("#linkToSentence" + sentenceId).val("");
                $(this).click(linkTo);
            },
            'html'
        );
    };

    $("#linkToSubmitButton" + sentenceId).unbind('click').click(linkTo);
    $("#linkTo" + sentenceId).toggle();
}
