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
        "$adminProfile has suspended $userProfile (id=$userId)."
    );
} else {
    echo $html->tag('p',
        "$adminProfile has changed the level of $userProfile (id=$userId) to -1."
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
    echo "<em>no one</em><br/>";
}

echo $html->tag('p', "The IP(s) under which $user has contributed are:");
echo '<ul>';
foreach($ips as $ip) {
    echo $html->tag('li',
        $ip['Contribution']['ip'] . ' ('. $ip[0]['count'] .' contributions)'
    );
}
echo '</ul>';