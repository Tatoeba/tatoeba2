var clipboard = new Clipboard('.copy-btn', {
    text: function(button) {
        var sentence = $(button).closest('.content')
                                .find('.sentenceContent > .text');
        return sentence.data('text') ? sentence.data('text') : sentence.text();
    }
});
clipboard.on('success', function(e) {
    e.trigger.className = 'copy-btn copied';
    setTimeout(copyDone, 500, e.trigger);
});

function copyDone(trigger) {
    trigger.className = 'copy-btn';
}