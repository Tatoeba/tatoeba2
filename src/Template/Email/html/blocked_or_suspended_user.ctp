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

echo $this->Html->tag('p', "The IP(s) under which $user has contributed are:");
echo '<ul>';
foreach($ips as $ip) {
    echo $this->Html->tag('li',
        $ip['Contribution']['ip'] . ' ('. $ip[0]['count'] .' contributions)'
    );
}
echo '</ul>';