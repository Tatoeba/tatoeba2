var clipboard = new Clipboard('.copy-btn');
clipboard.on('success', function(e) {
    e.trigger.className = 'copy-btn copied';
    setTimeout(copyDone, 500, e.trigger);
});

function copyDone(trigger) {
    trigger.className = 'copy-btn';
}