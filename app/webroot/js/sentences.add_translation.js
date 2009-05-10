$(document).ready(function() {
	$(".translateLink").click(function(){
		var sentence_id = $(this).attr("id");
		
		$("#translation_for_" + sentence_id).html('<li class="direct">'
			+ '<input id="'+ sentence_id +'_text" class="addTranslationsTextInput" type="text" value=""/>'
			+ '<input id="'+ sentence_id +'_submit" type="button" value="OK" />'
			+ '<input id="'+ sentence_id +'_cancel" type="button" value="Cancel" />'
			+ '</li>');
			
		$("#" + sentence_id + "_submit").click(function(){
			var sentence_text = $("#" + sentence_id + "_text").val();
			if($.trim(sentence_text) != ""){
				$("#translation_for_" + sentence_id).html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
				$.post("http://" + self.location.hostname + "/sentences/save_translation"
					, { "id": sentence_id, "value": sentence_text }
					, function(data){
						$(".addTranslations").html('');
						$("#" + sentence_id + "_translations").prepend("<li class='direct translation'>" + data + "</li>");
					}
					, "html"
				);
			}
		});
		
		$("#" + sentence_id + "_cancel").click(function(){
			$(".addTranslations").html('');
		});
	});
});