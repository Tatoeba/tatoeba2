$(document).ready(function(){
	$(".showStats").click(function(){
		$(".minorityLanguages").show();
		$(".sentencesStats").addClass("allStats");
		$(".statsDisplay").toggle();
	});
	
	$(".hideStats").click(function(){
		$(".minorityLanguages").hide();
		$(".sentencesStats").removeClass("allStats");
		$(".statsDisplay").toggle();
	});
});