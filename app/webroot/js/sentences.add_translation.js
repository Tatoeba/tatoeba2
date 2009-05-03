$(document).ready(function() {
	$(".translateLink").click(function(){
		var sentence_id = $(this).attr("id");
		
		$(".addTranslations").append('<li class="direct">'
			+ '<input type="text" value=""/>'
			+ '<input id="'+ sentence_id +'_submit" type="button" value="OK" />'
			+ '<input type="button" value="Cancel" />'
			+ '</li>');
			
		$("#" + sentence_id + "_submit").click(function(){
			$(".addTranslations").append('okokokok');
		});
	});
});