var clipboard = new Clipboard('.copy-btn', {
    text: function(button) {
        return $(button).closest('.content')
                        .find('.editableSentence')
                        .data('text');
    }
});
clipboard.on('success', function(e) {
    e.trigger.className = 'copy-btn copied';
    setTimeout(copyDone, 500, e.trigger);
});

function copyDone(trigger) {
    trigger.className = 'copy-btn';
}