$(document).ready(function(){
	loadRandom();
	
	$("#showRandom").click(function(){
		loadRandom();
	})
});

function loadRandom(){
	$(".random_sentences_set").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".random_sentences_set").load("http://" + self.location.hostname + "/" + $("#showRandom").attr("lang") + "/sentences/random");
}