/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

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

	function save(){
		var sentence_id = $(".translateLink").parent().attr("id");
		var sentence_lang = $(".translateLink").parent().attr("lang");
		alert (sentence_id + sentence_lang );

		var sentence_text = $("#" + sentence_id + "_text").val();
		//alert (sentence_text);
		if($.trim(sentence_text) != ""){
			$("#translation_for_" + sentence_id).html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
			$.post("http://" + self.location.hostname + "/sentences/save_translation"
				, { "id": sentence_lang+sentence_id, "value": sentence_text }
				, function(data){
					$(".same_language_warning").html('');
					$("#translation_for_" + sentence_id).html('');
					//alert(data);
					$("#" + sentence_id + "_translations").prepend(data);
				}
				, "html"
			);
		
		}
	}


	$("#are_you_sure_submit").click(function(){
		save();
		
	});


	$("#are_you_sure_cancel").click(function(){
		$(".same_language_warning").html('');
	});
});
