$(document).ready(function() {
	$('.editableSentence').editable('http://' + self.location.hostname + '/sentences/save_sentence', { 
		type      : 'textarea',
		cancel    : 'Cancel',
		submit    : 'OK',
		indicator : '<img src="/img/loading.gif">',
		tooltip   : 'Click to edit...'
	});
});
