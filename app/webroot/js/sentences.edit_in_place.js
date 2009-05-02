$(document).ready(function() {
	$('.editableSentence').editable('http://localhost/sentences/save_sentence', { 
		type      : 'textarea',
		cancel    : 'Cancel',
		submit    : 'OK',
		indicator : '<img src="/img/loading.gif">',
		tooltip   : 'Click to edit...'
	});
});
