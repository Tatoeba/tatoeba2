$(document).ready(function() {
	$("#submitNewSentence").click(function()	{
	    var valeur_de_l_input = $("#newSentenceText").val();
	    $.post("http://" + self.location.hostname + "/sentences/save_sentence"
		, { "value" : valeur_de_l_input }
		, function(data){			
			 $("#sentencesAdded").prepend(data);
		}
		, "html");	    
	})
});