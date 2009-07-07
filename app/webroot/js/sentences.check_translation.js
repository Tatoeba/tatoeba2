$(document).ready(function() {

	function save(){
		var sentence_id = $(".translateLink").attr("id");
		var sentence_lang = $(".translateLink").attr("lang");
		//alert (sentence_id + sentence_lang );

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
