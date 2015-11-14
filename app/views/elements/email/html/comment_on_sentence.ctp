<?php
$author = "<strong>$author</strong>";
if ($recipientIsOwner) {
    echo $html->tag('p', sprintf(
        '%s has posted a comment on one of your sentences.',
        $author
    ));
} else {
    echo $html->tag('p', sprintf(
        '%s has posted a comment on a sentence where you also posted a comment.',
        $author
    ));
}

echo $html->div(null, $messages->formatedContent($commentText),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);

echo "<p>";
echo $html->link($linkToSentence, $linkToSentence);
echo "</p>";
?>