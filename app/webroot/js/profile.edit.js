$(document).ready(function(){

	$('.x').click(function(){
		$(this).parent().parent().hide();
	});

	$('#pimg').click(function(){
		$('.toolbox').hide();
		$('#pimg_edit').show();
	});

	$('#pdescription_edit_link').click(function(){
		$('.toolbox').hide();
		$('#pdescription_edit').show();
	});

	$('#pbasic_edit_link').click(function(){
		$('.toolbox').hide();
		$('#pbasic_edit').show();
	});

	$('#pcontact_edit_link').click(function(){
		$('.toolbox').hide();
		$('#pcontact_edit').show();
	});

});