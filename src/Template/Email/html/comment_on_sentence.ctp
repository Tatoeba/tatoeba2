<?php
$sentenceId = $comment->sentence_id;
$urlParams = [
    'lang' => '',
    'controller' => 'sentence_comments',
    'action' => 'show',
    $sentenceId,
    '#' => 'comments'
];
$author = "<strong>$author</strong>";

if ($sentence) {
    echo $this->Html->tag('p', sprintf(
        "%s has posted a comment on the sentence '%s'.",
        $author,
        $sentence->text
    ));
} else {
    echo $this->Html->tag('p', sprintf(
        "%s has posted a comment on the deleted sentence #%d.",
        $author,
        $sentenceId
    ));
}


echo $this->Html->div(
    null,
    $this->Messages->formatContent($comment->text),
    [
        'style' => 'background:#f1f1f1;padding:20px',
    ]
);

$url = $this->Url->build($urlParams, true);
echo "<p>";
echo $this->Html->link($url);
echo "</p>";
?>