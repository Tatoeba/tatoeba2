<?php
$author = "<strong>$author</strong>";
echo $html->tag('p', sprintf(
    '%s has mentioned you in a comment.',
    $author
));

echo $html->div(null, $messages->formatedContent($commentText),
    array(
        'style' => 'background:#f1f1f1;padding:20px'
    )
);

echo "<p>";
echo $html->link($linkToComment, $linkToComment);
echo "</p>";
?>