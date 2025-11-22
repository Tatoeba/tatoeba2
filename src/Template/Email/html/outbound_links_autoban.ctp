<?php
$opts = ['fullBase' => true];
$what = $this->Html->link(
    sprintf('wall post containing %d or more outbound links', $threshold),
    [
        'lang' => '',
        'controller' => 'wall',
        'action' => 'show_message',
        $entity->id,
        '#' => "message_{$entity->id}",
    ],
    $opts
);
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

<p>User <strong><?= $this->Html->link($author->username, $profileUrl, $opts) ?></strong> was automatically banned after <?= $entity->isNew() ? "posting" : "editing" ?> a <?= $what ?>. Note that before posting it, <?= h($author->username) ?> affirmed the links are legitimate and not for SEO purposes. The content was automatically hidden.</p>

<p>You may <?= $this->Html->link("edit {$author->username}'s status", $userEditUrl, $opts) ?>.</p>