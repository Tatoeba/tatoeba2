/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 
function translationLink(action, sentenceId, translationId, langFilter)
{
    // Show the loading icon
    $('#link_' + sentenceId + '_' + translationId).blur().html(
        "<div class='loader-small loader'></div>"
    );
    $("#_" + sentenceId + "_message").remove();

    function success(data){
        var wasExpanded = !$("#_" + sentenceId + "_translations .showLink").is(":visible");
        $("#_" + sentenceId + "_translations").watch("replaceWith", data);
        if (wasExpanded) {
            $("#_" + sentenceId + "_translations .showLink").trigger("click");
        }
    }

    postLink(action, sentenceId, translationId, langFilter, success);
}
