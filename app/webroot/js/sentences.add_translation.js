$(document).ready(function() {
	$(".translateLink").click(function(){
		var sentence_id = $(this).attr("id");
		var sentence_lang = $(this).attr("lang");
		
		function save(){
			var sentence_text = $("#" + sentence_id + "_text").val();
			if($.trim(sentence_text) != ""){
				$("#translation_for_" + sentence_id).html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
				$.post("http://" + self.location.hostname + "/sentences/save_translation"
					, { "id": sentence_lang+sentence_id, "value": sentence_text }
					, function(data){
						$(".addTranslations").html('');
						$("#" + sentence_id + "_translations").prepend(data);
					}
					, "html"
				);
			}
		}
		
		$("#translation_for_" + sentence_id).html('<li class="direct">'
			+ '<input id="'+ sentence_id +'_text" class="addTranslationsTextInput" type="text" value=""/>'
			+ '<input id="'+ sentence_id +'_submit" type="button" value="OK" />'
			+ '<input id="'+ sentence_id +'_cancel" type="button" value="Cancel" />'
			+ '</li>');
		$("#" + sentence_id + "_text").focus();
			
		$("#" + sentence_id + "_submit").click(function(){
			save();
		});
		
		$("#translation_for_" + sentence_id + " li input").keypress(function(e){
			if(e.keyCode == 13) {
				save();
			}
		});
		
		$("#" + sentence_id + "_cancel").click(function(){
			$(".addTranslations").html('');
		});
	});
});