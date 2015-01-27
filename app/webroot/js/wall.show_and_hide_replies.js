function toggleReplies(messageId)
{
    if ($('#show_replies_button_' + messageId).is(':visible')) {
        
        $('#messageBody_' + messageId + ' > .thread').show();
        $('#hide_replies_button_' + messageId).show();
        $('#show_replies_button_' + messageId).hide();

    } else {

        $('#messageBody_' + messageId + ' > .thread').hide();
        $('#hide_replies_button_' + messageId).hide();
        $('#show_replies_button_' + messageId).show();

    }
}