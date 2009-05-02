$(document).ready(function(){
	loadRandom();
	loadRandomToTranslate();
	
	$("#showRandom").click(function(){
		loadRandom();
	})
	
	$("#showRandomToTranslate").click(function(){
		loadRandomToTranslate();
	})
});

function loadRandom(){
	$(".random_sentences_set").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".random_sentences_set").load("http://" + self.location.hostname + "/" + $("#showRandom").attr("lang") + "/sentences/random/show");
}

function loadRandomToTranslate(){
	$(".random_to_translate").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".random_to_translate").load("http://" + self.location.hostname + "/" + $("#showRandom").attr("lang") + "/sentences/random/translate");
}