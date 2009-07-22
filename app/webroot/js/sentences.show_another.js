$(document).ready(function(){
	var lang = $("#randomLangChoice").val();
	loadRandom(lang);
	loadRandomToTranslate(lang);
	
	$("#showRandom").click(function(){
		lang = $("#randomLangChoice").val();
		loadRandom(lang);
	})
	
	$("#showRandomToTranslate").click(function(){
		lang = $("#randomLangChoice").val();
		loadRandomToTranslate(lang);
	})
});

function loadRandom(lang){
	$(".random_sentences_set").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".random_sentences_set").load("http://" + self.location.hostname + "/" + $("#showRandom").attr("lang") + "/sentences/random/show/" + lang);
}

function loadRandomToTranslate(lang){
	$(".random_to_translate").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".random_to_translate").load("http://" + self.location.hostname + "/" + $("#showRandom").attr("lang") + "/sentences/random/translate/" + lang);
}