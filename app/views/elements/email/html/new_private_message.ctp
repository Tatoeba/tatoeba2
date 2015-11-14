<?php
echo $html->tag("p", "You have received a private message from <strong>$sender</strong>.");

echo $html->div(null,
    "<strong>Title:</strong> $title",
    array('style' => 'background:#666666;color:#ffffff;padding:10px;')
);

echo $html->div(null,
    $messages->formatedContent($message),
    array('style' => 'background:#f1f1f1;padding:10px;')
);

$replyLink = $html->link(
    "Reply",
    "https://tatoeba.org/private_messages/show/$messageId"
);
echo $html->tag("p", $replyLink);
?>