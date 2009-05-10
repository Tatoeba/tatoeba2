$(document).ready(function() {
	$('.editableSentence').editable('http://' + self.location.hostname + '/sentences/save_sentence', { 
		type      : 'text',
		cancel    : 'Cancel',
		submit    : 'OK',
		indicator : '<img src="/img/loading.gif">',
		tooltip   : 'Click to edit...',
		cssclass  : 'editInPlaceForm'
	});
});
