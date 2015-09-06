<?php
$profileUrl = 'https://tatoeba.org/user/profile/';
$adminProfile = $html->link(
    $admin,
    $profileUrl . $admin
);
$userProfile = $html->link(
    $user,
    $profileUrl . $user
);

if ($isSuspended) {
    echo $html->tag('p',
        "$adminProfile has suspended $userProfile."
    );
} else {
    echo $html->tag('p',
        "$adminProfile has changed the level of $userProfile to -1."
    );
}

echo $html->tag('p',
    "The password of $user matches the password of ".
    "the following suspended users: "
);
if (count($suspendedUsers) > 0) {
    echo '<ul>';
    foreach ($suspendedUsers as $suspendedUser) {
        echo '<li>';
        $suspendedUsername = $suspendedUser['User']['username'];
        echo $html->link(
            $suspendedUsername,
            $profileUrl . $suspendedUsername
        );
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo "<em>no one</em>";
}

echo $html->tag('p',
    "The last IP with which $user has contributed is: $lastIp."
);