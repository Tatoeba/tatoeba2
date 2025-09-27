<?php
$commentUrl = [
    'lang' => '',
    'controller' => 'sentences',
    'action' => 'show',
    $comment->sentence_id,
    '#' => "comment-{$comment->id}",
];
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
    $comment->user_id,
];
$opts = ['fullBase' => true];
?>

<p>User <strong><?= $this->Html->link($author->username, $profileUrl, $opts) ?></strong> has posted a <?= $this->Html->link('comment containing one or more outbound links', $commentUrl, $opts) ?>. Note that before posting this comment, <?= h($author->username) ?> confirmed the links are legitimate and not for SEO purposes.</p>

<p>You may <?= $this->Html->link("edit {$author->username}'s status", $userEditUrl, $opts) ?>.</p>