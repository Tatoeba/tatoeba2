var clipboard = new ClipboardJS('.copy-btn', {
    text: function(button) {
        var sentence = document.getElementById(button.dataset.targetId);
        return sentence.textContent;
    }
});
clipboard.on('success', function(e) {
    e.trigger.className = 'copy-btn copied';
    setTimeout(copyDone, 500, e.trigger);
});

function copyDone(trigger) {
    trigger.className = 'copy-btn';
}
