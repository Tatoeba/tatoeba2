<?php
$profileUrl = 'https://tatoeba.org/user/profile/';
$adminProfile = $this->Html->link(
    $admin,
    $profileUrl . $admin
);
$userProfile = $this->Html->link(
    $user,
    $profileUrl . $user
);

if ($isSuspended) {
    echo $this->Html->tag('p',
        "$adminProfile has suspended $userProfile (id=$userId)."
    );
} else {
    echo $this->Html->tag('p',
        "$adminProfile has changed the level of $userProfile (id=$userId) to -1."
    );
}
