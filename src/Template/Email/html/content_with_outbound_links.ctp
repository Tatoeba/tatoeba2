<?php
$opts = ['fullBase' => true];
if ($entity instanceof \App\Model\Entity\SentenceComment) {
    $what = $this->Html->link(
        'comment containing one or more outbound links',
        [
            'lang' => '',
            'controller' => 'sentences',
            'action' => 'show',
            $entity->sentence_id,
            '#' => "comment-{$entity->id}",
        ],
        $opts
    );
    $content = $entity->text;
} elseif ($entity instanceof \App\Model\Entity\Wall) {
    $what = $this->Html->link(
        'wall post containing one or more outbound links',
        [
            'lang' => '',
            'controller' => 'wall',
            'action' => 'show_message',
            $entity->id,
            '#' => "message_{$entity->id}",
        ],
        $opts
    );
    $content = $entity->content;
}
$profileUrl = [
    'lang' => '',
    'controller' => 'user',
    'action' => 'profile',
    $author->username,
];
$userEditUrl = [
    'lang' => '',
    'controller' => 'users',
    'action' => 'edit',
    $author->id,
];
?>

<p>User <strong><?= $this->Html->link($author->username, $profileUrl, $opts) ?></strong> has <?= $entity->isNew() ? "posted" : "edited" ?> a <?= $what ?>. Note that before posting it, <?= h($author->username) ?> affirmed the links are legitimate and not for SEO purposes.</p>

<p>You may <?= $this->Html->link("edit {$author->username}'s status", $userEditUrl, $opts) ?>.</p>

<?= $this->Html->div(
    null,
    $this->Messages->formatContent($content, false),
    [
        'style' => 'background:#f1f1f1;padding:20px',
    ]
) ?>
