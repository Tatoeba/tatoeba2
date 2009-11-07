/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$(document).ready(function() {

	$(".removeFromListButton").click(function(){
		var sentence_id = $(this).attr("id");
		var list_id = $(".sentencesList").attr("id");
		
		$("#sentence"+sentence_id).html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
		$("#sentence"+sentence_id).load("http://" + self.location.hostname + "/sentences_lists/remove_sentence_from_list/"+ sentence_id + "/" + list_id);
		
	});

});