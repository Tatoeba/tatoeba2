/*
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>
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

// TODO Can we delete this?

$(document).ready(function(){
    // use a little hack
    // we store the id of the sentence in a div
    sentenceId = $(".sentences_set").attr('id').slice(2);
   /* 

    $(".translations").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".translations").load("http://" + self.location.hostname + ":" + self.location.port + "/sentences/get_translations/" + sentenceId);
	*/
    		});
