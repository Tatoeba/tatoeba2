<?php
$author = "<strong>$author</strong>";

echo $html->tag('p', sprintf(
    "%s has posted a comment on sentence '%s'.",
    $author,
    $sentenceText
));


echo $html->div(null, $messages->formatedContent($commentText),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);

echo "<p>";
echo $html->link($linkToSentence, $linkToSentence);
echo "</p>";
?>