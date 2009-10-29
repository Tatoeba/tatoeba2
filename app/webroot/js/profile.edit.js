$(document).ready(function(){

	$('.x').click(function(){
		$(this).parent().parent().hide();
	});

	$('#pimg').click(function(){
		$('.toolbox').hide();
		$('#pimg_edit').show();
	});

	$('#pbasic_edit_link').click(function(){
		$('.toolbox').hide();
		$('#pbasic_edit').show();
	});

});