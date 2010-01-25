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

$(document).ready(function() {$("#clictest").unbind("click");

	var speakers = new Array();
	$(".SpeakerInput").each(function() {
		speakers.push($(this).attr("value"));
	});
	
	$(".SpeakerInput").autocomplete(speakers);
	
	$("#AddDialogLanguageLink").unbind("click");
	$("#AddDialogLanguageLink").click(function() {
		$("#AddDialogLanguageLink").hide();
		$("#AddDialogLanguageForm").show();
	});
	
	$("#addNewReply").unbind("click");
	$("#addNewReply").click(function() {
		$('#ConversationNbReplies').attr('value', parseInt($('#ConversationNbReplies').attr('value')) + 1);
		var newReply = new $("<div></div>");
		newReply.html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
		newReply.load("http://" + self.location.hostname + ":" + self.location.port + "/conversations/new_reply/" + $('#ConversationNbReplies').attr('value') + '/' + $('#ConversationLanguages').attr('value'));
		$('#sentencesList').append(newReply);
	});

	$("#DialogMainLanguage").unbind("change");
	$("#DialogMainLanguage").change(function() {
		if ($("#DialogMainLanguage").attr('value') != "") {
			$("#DialogEditor").load("http://" + self.location.hostname + ":" + self.location.port + "/conversations/new_dialog/" + $('#DialogMainLanguage').attr('value'));
			$("#AddDialogLanguageLink").show();
			$("#DialogMainLanguage").hide();
			var dialogElement = new $('<span class="DialogSelectedLanguage list-box"></span>');
			dialogElement.html($("#DialogMainLanguage option[value='" + $('#DialogMainLanguage').attr('value') +"']").text());
			dialogElement.append(new $('<a class="closebutton"></a>'));
			$("#LanguagesList").append(dialogElement);
			$("#DialogTranslationLanguage option[value='" + $('#DialogMainLanguage').attr('value') +"']").remove();
		}
	});
	
	$("#AddDialogLanguageForm").unbind("change");
	$("#AddDialogLanguageForm").change(function() {
		if ($("#DialogTranslationLanguage").attr('value') != "") {
			var sentence_pattern = new $('<tr></tr>');
			var url = "http://" + self.location.hostname + ":" + self.location.port + "/conversations/new_dialog_language/" + $('#DialogTranslationLanguage').attr('value');
			sentence_pattern.load(url, "", function() {
				$(".DialogSentenceLanguages").each(function() {
					var reply_id = parseInt($(this).attr("id").substring(24));
					var reply_language = sentence_pattern.clone();
					reply_language.find("input").each(function() {
						$(this).attr("id", $(this).attr("id") + reply_id);
						$(this).attr("name", "data[Conversation][content" + $('#DialogTranslationLanguage').attr('value') + reply_id + "]");
					});
					reply_language.find("label").each(function() {
						$(this).attr("for", $(this).attr("for") + reply_id);
					});
					$(this).append(reply_language);
				});
				$('#ConversationLanguages').attr('value', $('#ConversationLanguages').attr('value') + ";" + $('#DialogTranslationLanguage').attr('value'));
				$("#AddDialogLanguageLink").show();
				$("#AddDialogLanguageForm").hide();
				var dialogElement = new $('<span class="DialogSelectedLanguage list-box"></span>');
				dialogElement.html($("#DialogTranslationLanguage option[value='" + $('#DialogTranslationLanguage').attr('value') +"']").text());
				dialogElement.append(new $('<a class="closebutton"></a>'));
				$("#LanguagesList").append(dialogElement);
				
				
				$("#DialogTranslationLanguage option[value='" + $('#DialogTranslationLanguage').attr('value') +"']").remove();
			});
			
			var dialog_language_title = new $('<tr></tr>');
			url = "http://" + self.location.hostname + ":" + self.location.port + "/conversations/new_dialog_language_title/" + $('#DialogTranslationLanguage').attr('value');
			dialog_language_title.load(url, "", function() {
				$("#DialogTitleLanguages").append(dialog_language_title);
			});
		}
	});
	
	$("#ConversationAddForm").unbind("submit");
	$("#ConversationAddForm").submit(function() {
		var languages = $('#ConversationLanguages').attr('value').split(";");
		var nbReplies = parseInt($('#ConversationNbReplies').attr('value'));
		var valid = true;
		//alert(languages.length);
		for (var i = 0; i < languages.length; i++) {
			var language = languages[i];
			if ($("#ConversationTitle" + language).attr('value') == "") {
				$("#ConversationTitle" + language).css("background-color", "#f96767");
				valid = false;
			} else {
				$("#ConversationTitle" + language).css("background-color", "#FFFFFF");
			}
			for (var j = 1; j <= nbReplies; j++) {
				if ($("#ConversationContent" + language + j).attr('value') == "") {
					$("#ConversationContent" + language + j).css("background-color", "#f96767");
					valid = false;
				} else {
					$("#ConversationContent" + language + j).css("background-color", "#FFFFFF");
				}
			}
		}
		if (!valid) {
			return false;
		}
	});

});
