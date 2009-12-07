/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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

// this javascript is really dependant to id of divs which contains the id of the sentence
// for the moment, due to xhtml, the id is store that  id="_XXXX"  where XXXX is the sentence's id

	$(".translateLink").click(function(){
		var sentence_id = $(this).parent().attr("id").slice(1);
		var sentence_lang = $(this).parent().attr("lang");
		
		function save(){
			var sentence_text = $("#_" + sentence_id + "_text").val();
			if($.trim(sentence_text) != ""){

				$("#translation_for_" + sentence_id).html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
				$.post("http://" + self.location.hostname + "/sentences/check_translation"
					, { "id": sentence_id, "lang": sentence_lang, "value": sentence_text }
					, function(data){
						$(".addTranslations").html('');
						$("#_" + sentence_id + "_translations").prepend(data);
					}
					, "html"
				);

			}
		}


		$(".same_language_warning").html('');
		
		$("#translation_for_" + sentence_id).html('<li class="direct">'
			+ '<input id="_'+ sentence_id +'_text" class="addTranslationsTextInput" type="text" value=""/>'
			+ '<input id="_'+ sentence_id +'_submit" type="button" value="OK" />'
			+ '<input id="_'+ sentence_id +'_cancel" type="button" value="Cancel" />'
			+ '</li>');
		$("#_" + sentence_id + "_text").focus();
			
		$("#_" + sentence_id + "_submit").click(function(){
			save();
		});
		
		$("#translation_for_" + sentence_id + " li input").keypress(function(e){
			if(e.keyCode == 13) {
				save();
			}
		});
		
		$("#_" + sentence_id + "_cancel").click(function(){
			$(".addTranslations").html('');
		});
	});

});
