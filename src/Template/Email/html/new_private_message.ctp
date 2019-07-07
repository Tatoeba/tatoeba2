<?php
echo $this->Html->tag("p", "You have received a private message from <strong>$sender</strong>.");

echo $this->Html->div(null,
    "<strong>Title:</strong> $title",
    array('style' => 'background:#666666;color:#ffffff;padding:10px;')
);

echo $this->Html->div(null,
    $this->Messages->formatContent($message),
    array('style' => 'background:#f1f1f1;padding:10px;')
);

$replyLink = $this->Html->link(
    "Reply",
    "https://tatoeba.org/private_messages/show/$messageId"
);
echo $this->Html->tag("p", $replyLink);
?>