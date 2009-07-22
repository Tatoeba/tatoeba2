$(document).ready(function(){
	$("#randomLangChoiceInBrowse").change(function(){
		var lang = $(this).val();
		$("#randomLink").attr("href", "http://" + self.location.hostname + "/" + $("#randomLink").attr("lang") + "/sentences/show/" + lang);
	});
});