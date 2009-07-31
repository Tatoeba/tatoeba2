$(document).ready(function(){
	var lang = $("#randomLangChoice").val();
	if(lang == null) lang = '';
	
	loadRandom(lang);
	
	$("#showRandom").click(function(){
		lang = $("#randomLangChoice").val();
		loadRandom(lang);
	})
});

function loadRandom(lang){
	$(".random_sentences_set").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".random_sentences_set").load("http://" + self.location.hostname + "/" + $("#showRandom").attr("lang") + "/sentences/random/show/" + lang);
}