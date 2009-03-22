$(document).ready(function(){
	$("#test").click(function(event){
		$("#test_result").load("http://localhost/tatoeba/sentences/show/1");
	});
});