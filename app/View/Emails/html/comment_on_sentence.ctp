<?php
$author = "<strong>$author</strong>";

if ($sentenceIsDeleted) {
    echo $this->Html->tag('p', sprintf(
        "%s has posted a comment on deleted sentence #%d.",
        $author,
        $sentenceId
    ));
} else {
    echo $this->Html->tag('p', sprintf(
        "%s has posted a comment on sentence '%s'.",
        $author,
        $sentenceText
    ));
}


echo $this->Html->div(null, $this->Messages->formatedContent($commentText),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);

echo "<p>";
echo $this->Html->link($linkToSentence, $linkToSentence);
echo "</p>";
?>